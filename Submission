
import java.io.*;
import java.nio.file.*;
import java.nio.file.attribute.BasicFileAttributes;

public class ReportGenerator {

    public static void generate(Path folder, Path report)
            throws IOException {

        long deposits = 0;
        long withdrawals = 0;
        StringBuilder details = new StringBuilder();

        try (DirectoryStream<Path> files =
                Files.newDirectoryStream(folder)) {

            for (Path file : files) {
                if (!Files.isRegularFile(file))
                    continue;

                BasicFileAttributes attr = Files.readAttributes(
                        file, BasicFileAttributes.class);

                details.append(file.getFileName())
                       .append(" | Size: ").append(attr.size())
                       .append(" bytes\n");

                try (BufferedReader reader =
                        Files.newBufferedReader(file)) {

                    String line;
                    while ((line = reader.readLine()) != null) {
                        String[] parts = line.trim().split("\\s+");

                        if (parts.length != 2)
                            continue;

                        long amount = Long.parseLong(parts[1]);

                        if (parts[0].equals("DEPOSIT"))
                            deposits += amount;
                        else if (parts[0].equals("WITHDRAW"))
                            withdrawals += amount;
                    }
                }
            }
        }

        String summary = "END-OF-DAY REPORT\n"
                + "Total Deposits: " + deposits + "\n"
                + "Total Withdrawals: " + withdrawals + "\n"
                + "Log File Details:\n" + details;

        Files.write(report, summary.getBytes());
    }
}
