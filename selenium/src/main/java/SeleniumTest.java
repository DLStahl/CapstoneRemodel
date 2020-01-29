import io.github.bonigarcia.wdm.WebDriverManager;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;

import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;

public class SeleniumTest {

    public static WebDriver driver;

    public static int passed;
    public static int failed;
    public static int total;

    public static void assertTrue(boolean flag, String text) {
        total ++;
        if(flag) {
            passed ++;
            System.out.println("[PASS] " + text);
        } else {
            failed ++;
            System.out.println("[FAILED] " + text);
        }
    }

    public static void main(String[] args) throws Exception {
        WebDriverManager.chromedriver().setup();

        ChromeOptions options = new ChromeOptions();
        options.addArguments("--start-maximized");
        options.setAcceptInsecureCerts(true);

        driver = new ChromeDriver(options);
        driver.get("https://remodel.anesthesiology-dev.org.ohio-state.edu/");

        confirmSignInMessage();

        signIn();
        System.out.println("Sign in for me as admin, I'm not smart enough");
        Scanner scanner = new Scanner(System.in);
        scanner.next();

        confirmLandingPage();
        driver.get("https://remodel.anesthesiology-dev.org.ohio-state.edu/laravel/public/resident/schedule/firstday");
        topRoomIsUH();
        staticOptionsExist();
        allRotationFiltersExist();
        confirmFilterWorks();

        confirmAdminDashboardWorks();

        driver.close();

        System.out.println("Passed: " + passed + "/" + total + ", Failed: " + failed);
    }

    private static void confirmFilterWorks() {
        WebElement clearFilterElement = driver.findElement(By.cssSelector("#filter > div > button"));
        assertTrue(clearFilterElement.getText().equals("Clear Filter"), "Clear filter button exists");

        List<WebElement> topShowingRooms = driver.findElements(By.cssSelector("#schedule_table > div > div.sked-tape__aside > div.sked-tape__locations > div.collapse.show.sked-tape__collapse > div"));
        int initialRoomCount = topShowingRooms.size();

        // confirm room filter works
        WebElement uh01Filter = driver.findElement(By.cssSelector("#room > option:nth-child(2)"));
        uh01Filter.click();

        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}

        // confirm only UH-01 is showing
        topShowingRooms = driver.findElements(By.cssSelector("#schedule_table > div > div.sked-tape__aside > div.sked-tape__locations > div.collapse.show.sked-tape__collapse > div"));
        String topShowingRoomTitle = topShowingRooms.get(0).getAttribute("title");

        assertTrue(topShowingRoomTitle.contains("UH"), "top room is UH");
        assertTrue(topShowingRooms.size() == 1, "only one room is showing when filtered on it");

        // test clear filter, just see if the new number it shows is the same as the old one
        clearFilterElement.click();

        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}

        topShowingRooms = driver.findElements(By.cssSelector("#schedule_table > div > div.sked-tape__aside > div.sked-tape__locations > div.collapse.show.sked-tape__collapse > div"));

        assertTrue(topShowingRooms.size() == initialRoomCount, "Clear filter correctly reveals everything");

        // confirm OORA rotation filter works

        List<WebElement> rotationFilterOptions = driver.findElements(By.cssSelector("#rotation > option"));
        rotationFilterOptions.forEach(x -> {
            if(x.getAttribute("value").equals("OORA")) {
                x.click();
            }
        });

        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}

        topShowingRooms = driver.findElements(By.cssSelector("#schedule_table > div > div.sked-tape__aside > div.sked-tape__locations > div.collapse.show.sked-tape__collapse > div"));

        assertTrue(topShowingRooms.size() == 5, "All 5 OORA events are showing");
    }

    private static void confirmAdminDashboardWorks() {
        String[] expectedLinks = { "Edit Users", "Edit Schedules", "Edit Milestones", "Post Messages", "Download Data Sheets", "Reset Tickets", "View Resident/Attending Pairings", "Upload Schedule", "MedHub Test", "Filter Rotations"};
        String[] expectedLocation = {"users","schedules","milestones","postmessage","download","resetTickets","evaluation","uploadForm","medhubtest","filterrotation"};

        // confirm the link is there
        WebElement adminDashboard = driver.findElement(By.id("dashboard"));
        assertTrue(adminDashboard != null, "Admin dashboard is a link");

        // confirm it shows on click
        adminDashboard.click();
        WebElement sideNav = driver.findElement(By.id("adminsidenav"));

        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}

        assertTrue(driver.findElement(By.id("adminsidenav")).getAttribute("style").equals("width: 100%;"), "Side nav appears when clicked");

        // confirm all the links
        List<WebElement> adminDashboardLinks = driver.findElements(By.cssSelector("#adminsidenav > a"));
        List<String> adminDashboardLinkNames = new ArrayList<>();
        adminDashboardLinks.forEach(x -> adminDashboardLinkNames.add(x.getText()));


        for (String expectedLink : expectedLinks) {
            assertTrue(adminDashboardLinkNames.contains(expectedLink), expectedLink + " exists in the admin dashboard.");
        }

        WebElement closeDashboardElement = driver.findElement(By.className("closebtn"));
        assertTrue(closeDashboardElement != null, "Close dashboard button exists");
        closeDashboardElement.click();

        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}

        assertTrue(driver.findElement(By.id("adminsidenav")).getAttribute("style").equals("width: 0px;"), "Close dashboard button correctly closes the dashboard");

        int linkIndex = 0;
        while(true) {
            // confirm all links work
            adminDashboard = driver.findElement(By.id("dashboard"));
            adminDashboard.click();

            try {
                Thread.sleep(1000);
            } catch (Exception e) { /* ignored */}

            adminDashboardLinks = driver.findElements(By.cssSelector("#adminsidenav > a"));

            // +1 so we skip the close button
            adminDashboardLinks.get(linkIndex + 1).click();

            while (!driver.getCurrentUrl().contains(expectedLocation[linkIndex])) try {
                Thread.sleep(1000);
            } catch (Exception e) { /* ignored */}

            assertTrue(driver.getCurrentUrl().contains(expectedLocation[linkIndex]), expectedLinks[linkIndex] + " takes you to " + expectedLocation[linkIndex]);

            driver.navigate().back();

            if(adminDashboardLinks.size() - 2 == linkIndex) break;
            linkIndex ++;
        }
    }

    private static void allRotationFiltersExist() {
        String[] rotationsExpected = { "Basic", "Neuro", "Liver", "Thoracic", "Cardiac", "Vascular", "OORA" };
        for (int i = 0; i < rotationsExpected.length; i ++) {
            // this is the HTML child index
            int index = i + 2;
            String rotation = rotationsExpected[i];

            String message = rotation + " rotation filter found.";

            WebElement rotationElement = null;

            try {
                rotationElement = driver.findElement(By.cssSelector("#rotation > option:nth-child(" + index + ")"));
            } catch (Exception e) {
                System.out.println(e.getMessage());
                assertTrue(false, message);
            }

            if(rotationElement != null) {
                assertTrue(rotationElement.getAttribute("value").equals(rotation), message);
            }
        }
    }

    private static void staticOptionsExist() {
        // get all room names (left side)
        List<WebElement> rooms = driver.findElements(By.className("sked-tape__location-text"));
        List<String> roomsText = new ArrayList<>();
        rooms.forEach(x -> roomsText.add(x.getText()));
        // get all event titles (blue scheduled events)
        List<WebElement> roomEvents = driver.findElements(By.className("sked-tape__event"));
        List<String> roomEventTitles = new ArrayList<>();
        roomEvents.forEach(x -> roomEventTitles.add(x.getAttribute("title")));

        String[] staticOptions = {"ECT", "Endo5", "IR2", "MRI", "Pulmonary"};
        for (String staticOption :
                staticOptions) {
            assertTrue(roomsText.contains(staticOption), staticOption + " found in the list of rooms.");
            assertTrue(roomEventTitles.contains(staticOption + "-1"), staticOption + " found in the list of events.");
        }
    }

    private static void topRoomIsUH() {
        WebElement topRoomFilter = driver.findElement(By.cssSelector("#room > option:nth-child(2)"));
        assertTrue(topRoomFilter.getAttribute("value").contains("UH"), "Top room filter is UH");

        WebElement topRoomNameElement = driver.findElement(By.className("sked-tape__location-text"));
        String topRoomName = topRoomNameElement.getText();
        assertTrue(topRoomName.contains("UH"), "Top room name is UH");
    }

    private static void confirmSignInMessage() {
        assertTrue(driver.getCurrentUrl().endsWith(".edu/"), "Login page loads");

        WebElement messageElement = driver.findElement(By.cssSelector("body > div.container > h6"));
        String message = messageElement.getText();
        assertTrue(message.contains("system is designed to allow residents to identify preferences for scheduled"), "Login page has intro message");
    }

    public static void confirmLandingPage() {
        String currentURL = driver.getCurrentUrl();
        assertTrue(currentURL.endsWith("secondday"), "Landing page is secondday");
    }

    public static void signIn() {
        WebElement signInButton = driver.findElement(By.cssSelector("body > div.container > a"));
        signInButton.click();
    }
}
