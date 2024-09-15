# Define variables
$vcRedistUrl = "https://github.com/abbodi1406/vcredist/releases/download/v0.83.0/VisualCppRedist_AIO_x86only.exe"
$wampServerUrl = "https://wampserver.aviatechno.net/files/install/wampserver3.3.5_x64.exe"
$composerUrl = "https://getcomposer.org/Composer-Setup.exe"
$downloadsPath = [System.IO.Path]::Combine([System.Environment]::GetFolderPath('UserProfile'), 'Downloads')
$vcRedistPath = Join-Path $downloadsPath "VisualCppRedist_AIO_x86only.exe"
$wampServerPath = Join-Path $downloadsPath "wampserver3.3.5_x64.exe"
$composerSetupPath = Join-Path $downloadsPath "Composer-Setup.exe"

# Function to print section title and progress
function Print-Title {
    param (
        [string]$title,
        [int]$step,
        [int]$totalSteps
    )
    Write-Host "`n==================== Step $step/$totalSteps: $title ====================" -ForegroundColor Yellow
}

# Function to check permissions
function Check-Permissions {
    param (
        [string]$path
    )
    try {
        $null = New-Item -Path $path -ItemType File -Name "permission_test.tmp" -ErrorAction Stop
        Remove-Item -Path "$path\permission_test.tmp" -Force
        return $true
    }
    catch {
        return $false
    }
}

# Function to download file
function Download-File {
    param (
        [string]$url,
        [string]$outputPath
    )
    try {
        Write-Host "Downloading from $url to $outputPath..." -ForegroundColor Cyan
        Invoke-WebRequest -Uri $url -OutFile $outputPath -ErrorAction Stop
        Write-Host "Download completed successfully." -ForegroundColor Green
    }
    catch {
        Write-Host "Error downloading file: $_" -ForegroundColor Red
        exit
    }
}

# Function to install software
function Install-Software {
    param (
        [string]$filePath,
        [string]$softwareName,
        [string]$arguments = "/SILENT"
    )
    try {
        Write-Host "Installing $softwareName..." -ForegroundColor Cyan
        $process = Start-Process -FilePath $filePath -ArgumentList $arguments -Wait -PassThru
        if ($process.ExitCode -eq 0) {
            Write-Host "$softwareName installed successfully." -ForegroundColor Green
        }
        else {
            throw "$softwareName installation failed with exit code $($process.ExitCode)"
        }
    }
    catch {
        Write-Host "Error installing $softwareName: $_" -ForegroundColor Red
        exit
    }
}

# Function to add Composer to PATH
function Add-To-Path {
    $composerPath = [System.IO.Path]::Combine($env:LOCALAPPDATA, "ComposerSetup", "bin")
    try {
        Write-Host "Adding Composer to PATH..." -ForegroundColor Cyan
        [System.Environment]::SetEnvironmentVariable("PATH", $env:PATH + ";$composerPath", [System.EnvironmentVariableTarget]::Machine)
        Write-Host "Composer added to PATH successfully." -ForegroundColor Green
    }
    catch {
        Write-Host "Error adding Composer to PATH: $_" -ForegroundColor Red
    }
}

# Function to verify installation
function Verify-Installation {
    param (
        [string]$command,
        [string]$softwareName
    )
    try {
        $null = Get-Command $command -ErrorAction Stop
        Write-Host "$softwareName is installed and accessible from the command line." -ForegroundColor Green
        return $true
    }
    catch {
        Write-Host "$softwareName is not accessible from the command line. Please check your installation." -ForegroundColor Red
        return $false
    }
}

# Main script
$totalSteps = 6
$currentStep = 0

Print-Title "Script Execution Started" ($currentStep++) $totalSteps

# Check Permissions
Print-Title "Checking Permissions" ($currentStep++) $totalSteps
if (-not (Check-Permissions -path $downloadsPath)) {
    Write-Host "You do not have permissions to access the Downloads folder." -ForegroundColor Red
    exit
}
Write-Host "Permissions are sufficient for the Downloads folder." -ForegroundColor Green

# Download and Install VC++ Redistributables
Print-Title "Installing VC++ Redistributables" ($currentStep++) $totalSteps
Download-File -url $vcRedistUrl -outputPath $vcRedistPath
Install-Software -filePath $vcRedistPath -softwareName "VC++ Redistributables"

# Download and Start WampServer Installation
Print-Title "Starting WampServer Installation" ($currentStep++) $totalSteps
Download-File -url $wampServerUrl -outputPath $wampServerPath
Write-Host "Launching WampServer installer. Please follow the on-screen instructions to complete the installation." -ForegroundColor Cyan
Start-Process -FilePath $wampServerPath -Wait

# Prompt user to confirm WAMP installation
do {
    $wampInstalled = Read-Host "Have you completed the WAMP Server installation? (Y/N)"
} while ($wampInstalled -notmatch '^[YyNn]$')

if ($wampInstalled -match '^[Yy]$') {
    Write-Host "Continuing with Composer installation..." -ForegroundColor Green
}
else {
    Write-Host "WAMP Server installation not completed. Exiting script." -ForegroundColor Red
    exit
}

# Download and Install Composer
Print-Title "Installing Composer" ($currentStep++) $totalSteps
Download-File -url $composerUrl -outputPath $composerSetupPath
Install-Software -filePath $composerSetupPath -softwareName "Composer"

# Add Composer to PATH
Add-To-Path

# Verify Installations
Print-Title "Verifying Installations" ($currentStep++) $totalSteps
$phpInstalled = Verify-Installation -command "php" -softwareName "PHP (via WampServer)"
$mysqlInstalled = Verify-Installation -command "mysql" -softwareName "MySQL (via WampServer)"
$composerInstalled = Verify-Installation -command "composer" -softwareName "Composer"

# Final confirmation
if ($phpInstalled -and $mysqlInstalled -and $composerInstalled) {
    Write-Host "`nAll components (WAMP Server, PHP, MySQL, and Composer) have been successfully installed and verified." -ForegroundColor Green
}
else {
    Write-Host "`nSome components may not have been installed correctly. Please review the messages above and consider reinstalling any missing components." -ForegroundColor Yellow
}

# Clean up downloaded files
Remove-Item -Path $vcRedistPath -Force
Remove-Item -Path $wampServerPath -Force
Remove-Item -Path $composerSetupPath -Force

Write-Host "`nSetup completed. Temporary files have been removed." -ForegroundColor Green