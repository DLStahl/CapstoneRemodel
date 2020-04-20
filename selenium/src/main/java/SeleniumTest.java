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
        //Scanner scanner = new Scanner(System.in);
        //scanner.next();
        Thread.sleep(21000);
        System.out.println("Awake");
        confirmLandingPage();
        driver.get("https://remodel.anesthesiology-dev.org.ohio-state.edu/laravel/public/resident/schedule/firstday");
        topRoomIsUH();
        staticOptionsExist();
        allRotationFiltersExist();
        confirmFilterWorks();
        confirmAdminDashboardWorks();
        confirmResidentTable();
        confirmDbEditorFeatures();
        confirmFormsTable();
        confirmDbEditorFeatures();

        driver.close();

        System.out.println("Passed: " + passed + "/" + total + ", Failed: " + failed);
    }

    private static void confirmFilterWorks() {
        WebElement clearFilterElement = driver.findElement(By.cssSelector("#filter > div.float-right > button"));
        assertTrue(clearFilterElement.getText().equals("Clear Filter"), "Clear filter button exists");

        WebElement roomFilterElement = driver.findElement(By.cssSelector("#filter > div.dropdown.keep-inside-clicks-open > button"));
        assertTrue(roomFilterElement.getText().equals("Room Filter"), "Clear filter button exists");
        roomFilterElement.click();

        List<WebElement> topShowingRooms = driver.findElements(By.cssSelector("#schedule_table > div > div.sked-tape__aside > div.sked-tape__locations > div.collapse.show.sked-tape__collapse > div"));
        int initialRoomCount = topShowingRooms.size();

        // confirm room filter works
        WebElement allFilter = driver.findElement(By.cssSelector("#allRoomFilter > label"));
        allFilter.click();
        WebElement uh01Filter = driver.findElement(By.cssSelector("#UH-01_dropdownFilterDiv"));
        uh01Filter.click();

        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}

        // confirm only UH-01 is showing
        topShowingRooms = driver.findElements(By.cssSelector("#schedule_table > div > div.sked-tape__aside > div.sked-tape__locations > div.collapse.show.sked-tape__collapse > div"));
        String topShowingRoomTitle = topShowingRooms.get(0).getAttribute("title");

        assertTrue(topShowingRoomTitle.contains("UH"), "top room is UH");
        assertTrue(topShowingRooms.size() == 1, "only one room is showing when filtered on it");

        //UH rooms filter
        WebElement uhFilter = driver.findElement(By.cssSelector("#UH_dropdownFilterDiv > label"));
        uhFilter.click();
        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}

        // confirm only UH rooms showing
        topShowingRooms = driver.findElements(By.cssSelector("#schedule_table > div > div.sked-tape__aside > div.sked-tape__locations > div.collapse.show.sked-tape__collapse > div"));
        topShowingRoomTitle = topShowingRooms.get(0).getAttribute("title");

        assertTrue(topShowingRoomTitle.contains("UH"), "top room is UH");
        assertTrue(topShowingRooms.size() == 13, "all UH rooms are showing");

        uhFilter.click();
        WebElement ectFilter = driver.findElement(By.cssSelector("#ECT_dropdownFilterDiv > label"));
        ectFilter.click();
        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}

        // confirm only ECT showing
        topShowingRooms = driver.findElements(By.cssSelector("#schedule_table > div > div.sked-tape__aside > div.sked-tape__locations > div.collapse.show.sked-tape__collapse > div"));
        topShowingRoomTitle = topShowingRooms.get(0).getAttribute("title");

        assertTrue(topShowingRoomTitle.contains("ECT"), "top room is ECT");
        assertTrue(topShowingRooms.size() == 1, "only one ECT room is showing when filtered");

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
        String[] expectedLinks = { "Edit Residents", "Edit Attendings", "Edit Surgeons/Rotations", "Edit Admins", "Edit Evaluation Forms", "Edit Static Schedule Data", "Edit Variables", "Edit Schedules", "Edit Milestones", "Post Messages", "Download Data Sheets", "Reset Tickets", "View Resident/Attending Pairings", "Upload Schedule", "MedHub Test"};
        String[] expectedLocation = {"resident","attending","rotation","admin","evaluation_forms","schedule_data_static","variables","schedules","milestones","postmessage","download","resetTickets","evaluation","uploadForm","medhubtest"};

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
        //WebElement topRoomFilter = driver.findElement(By.cssSelector("#UH_dropdownFilterDiv > label"));
        //assertTrue(topRoomFilter.getAttribute("value").contains("UH"), "Top room filter is UH");

        WebElement topRoomNameElement = driver.findElement(By.className("sked-tape__location-text"));
        String topRoomName = topRoomNameElement.getText();
        assertTrue(topRoomName.contains("UH"), "Top room name is UH");
    }

    private static void confirmResidentTable() {
    	driver.get("https://remodel.anesthesiology-dev.org.ohio-state.edu/laravel/public/admin/db/resident");

    	WebElement residentTableName = driver.findElement(By.cssSelector("#table > thead > tr > th:nth-child(2)"));
    	assertTrue(residentTableName.getText().equals("name"), "Resident Table has name field");

    	WebElement residentTableEmail = driver.findElement(By.cssSelector("#table > thead > tr > th:nth-child(3)"));
    	assertTrue(residentTableEmail.getText().equals("email"), "Resident Table has email field");

    	WebElement residentTableMedhubId = driver.findElement(By.cssSelector("#table > thead > tr > th:nth-child(5)"));
    	assertTrue(residentTableMedhubId.getText().equals("medhubId"), "Resident Table has medhubId field");

    	WebElement residentTableEntry = driver.findElement(By.cssSelector("#\\31  > td:nth-child(3)"));
    	assertTrue(residentTableEntry.getText().contains("@osu.edu"), "Resident Table has entry in table with email");

    }

    private static void confirmFormsTable() {
    	driver.get("https://remodel.anesthesiology-dev.org.ohio-state.edu/laravel/public/admin/db/evaluation_forms");

    	WebElement formsTableType = driver.findElement(By.cssSelector("#table > thead > tr > th:nth-child(2)"));
    	assertTrue(formsTableType.getText().equals("form_type"), "Forms Table has form_type");

    	WebElement formsTableName = driver.findElement(By.cssSelector("#table > thead > tr > th:nth-child(3)"));
    	assertTrue(formsTableName.getText().equals("medhub_form_name"), "Forms Table has medhub_form_name");

    	WebElement formsTableId = driver.findElement(By.cssSelector("#table > thead > tr > th:nth-child(4)"));
    	assertTrue(formsTableId.getText().equals("medhub_form_id"), "Forms Table has medhub_form_id");

    	WebElement residentTableEntry = driver.findElement(By.cssSelector("#\\31  > td:nth-child(3)"));
    	assertTrue(residentTableEntry.getText().equals("Acute Pain Service"), "Acute Pain Service exists in table");

    }

    private static void confirmDbEditorFeatures() {
    	WebElement addButton = driver.findElement(By.cssSelector("body > div.container > button"));
    	assertTrue(addButton.getText().contains("Add"), "Add button exists");
    	addButton.click();
    	try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}
    	WebElement addModal = driver.findElement(By.cssSelector("#addModal > div"));
        assertTrue(addModal.getCssValue("display").equals("flex"), "Add popup appears when clicked");
        WebElement closeAdd = driver.findElement(By.cssSelector("#addModal > div > div > div.modal-header > button > span"));
        closeAdd.click();
        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}
        WebElement addCheck = driver.findElement(By.cssSelector("#addModal"));
        assertTrue(addCheck.getCssValue("display").equals("none"), "Add popup closes when x is clicked");

    	WebElement editButton = driver.findElement(By.cssSelector("#\\31  > td.b1.operation > button"));
    	assertTrue(editButton.getText().contains("Edit"), "Edit button exists");
        editButton.click();
        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}
        WebElement editModal = driver.findElement(By.cssSelector("#editModal > div"));
        assertTrue(editModal.getCssValue("display").equals("flex"), "Edit popup appears when clicked");
        WebElement closeEdit = driver.findElement(By.cssSelector("#editModal > div > div > div.modal-header > button > span"));
        closeEdit.click();
        try {
            Thread.sleep(1000);
        } catch (Exception e) { /* ignored */}

        WebElement closeCheck = driver.findElement(By.cssSelector("#editModal"));
        assertTrue(closeCheck.getCssValue("display").equals("none"), "Edit popup closes when x is clicked");
    }

    private static void confirmSignInMessage() {
        assertTrue(driver.getCurrentUrl().endsWith(".edu/"), "Login page loads");

        WebElement messageElement = driver.findElement(By.cssSelector("body > div.container > h6"));
        String message = messageElement.getText();
        assertTrue(message.contains("system is designed to allow residents to identify preferences for scheduled"), "Login page has intro message");
    }

    public static void confirmLandingPage() {
        String currentURL = driver.getCurrentUrl();
        System.out.println(currentURL);
        assertTrue(currentURL.endsWith("secondday"), "Landing page is secondday");
    }

    public static void signIn() {
        WebElement signInButton = driver.findElement(By.cssSelector("body > div.container > a"));
        signInButton.click();
    }
}
