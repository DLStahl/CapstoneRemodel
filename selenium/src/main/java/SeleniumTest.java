import io.github.bonigarcia.wdm.WebDriverManager;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;

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
            System.out.println("[FAILED]" + text);
        }
    }

    public static void main(String[] args) throws Exception {
        WebDriverManager.chromedriver().setup();

        ChromeOptions options = new ChromeOptions();
        options.setAcceptInsecureCerts(true);

        driver = new ChromeDriver(options);
        driver.get("https://remodel.anesthesiology-dev.org.ohio-state.edu/");

        signIn();
        System.out.println("Sign in for me, I'm not smart enough");
        Scanner scanner = new Scanner(System.in);
        scanner.next();

        confirmLandingPage();

    }

    public static void confirmLandingPage() {
        String currentURL = driver.getCurrentUrl();
        assertTrue(currentURL.endsWith("secondday"), "Landing page is secondday");
    }

    public static void signIn() {
        WebElement signInButton = driver.findElement(By.cssSelector("body > div.container > a"));
        signInButton.click();
        System.out.println("Clicked sign in");
    }
}
