@echo off
setlocal

set PORT=8001
if not "%~1"=="" set PORT=%~1

set "PROJECT_ROOT=%~dp0"
set "PUBLIC_DIR=%PROJECT_ROOT%public"
set "ROUTER=%PUBLIC_DIR%\index.php"

where php >nul 2>nul
if errorlevel 1 (
    echo PHP is not installed or not available in PATH.
    exit /b 1
)

if not exist "%PUBLIC_DIR%" (
    echo Public directory not found: %PUBLIC_DIR%
    exit /b 1
)

if not exist "%ROUTER%" (
    echo Router file not found: %ROUTER%
    exit /b 1
)

echo Starting PHP dev server at http://127.0.0.1:%PORT%
echo Document root: %PUBLIC_DIR%
echo Press Ctrl+C to stop.

cd /d "%PROJECT_ROOT%"
php -S 127.0.0.1:%PORT% -t "%PUBLIC_DIR%"
