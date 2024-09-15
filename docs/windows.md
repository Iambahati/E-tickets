# WAMP Server and Composer Setup Instructions
## Prerequisites

Before running the script, ensure you have the following:

1. **Windows 10 or later**: 

2. **PowerShell 5.1 or later**: To check your PowerShell version, open PowerShell and run:
   ```powershell
   $PSVersionTable.PSVersion
   ```

3. **Administrator privileges**: The script requires elevated permissions to install software and modify system settings.

4. **Internet connection**: The script will download necessary installers.

5. **Sufficient disk space**: Ensure you have at least 2GB of free space on your system drive.

6. **No conflicting software**: Ensure no other web servers or MySQL instances are running on your system.

## Running the Script

Follow these steps to run the `Setup-WAMP-Composer-Environment.ps1` script:

1. **Open PowerShell as Administrator**:
   - Press `Win + X` and select "Windows PowerShell (Admin)" or "Terminal (Admin)".

2. **Set Execution Policy**:
   Run the following command to allow script execution:
   ```powershell
   Set-ExecutionPolicy RemoteSigned -Scope Process
   ```
   This sets the execution policy for the current PowerShell session only.

3. **Navigate to Script Directory**:
   Use the `cd` command to navigate to the root folder containing this project:
   ```powershell
   cd path\to\script\directory
   ```

4. **Run the Script**:
   Execute the script by typing:
   ```powershell
   .\Setup-WAMP-Composer-Environment.ps1
   ```

5. **Follow On-Screen Instructions**:
   - The script will guide you through the installation process.
   - When prompted, complete the WAMP Server installation manually.
   - Confirm when you've finished the WAMP installation to allow the script to continue.

6. **Verify Installation**:
   The script will verify the installation of PHP, MySQL, and Composer.

## Post-Installation

After the script completes:

1. Restart your computer to ensure all changes take effect.
2. Test your WAMP Server installation by accessing `http://localhost` in your web browser.
3. Verify Composer installation by opening a new PowerShell window and running `composer --version`.

If you encounter any issues, review the script output for error messages and consider running the script again or performing a manual installation of the problematic components.

## Troubleshooting

- If you receive a "File Not Found" error, ensure you're in the correct directory containing the script.
- If you get a security error, you may need to unblock the script file. Right-click the file, select Properties, and check "Unblock" if present.
- For any other issues, check the console output for specific error messages and refer to the WAMP or Composer documentation for further assistance.