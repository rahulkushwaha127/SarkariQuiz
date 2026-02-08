# Fix broken NDK: remove incomplete/corrupt NDK folder so Gradle can re-download.
# Run this if you see: "NDK at ... did not have a source.properties file"

$ndkPath = "$env:LOCALAPPDATA\Android\Sdk\ndk\27.0.12077973"
if (Test-Path $ndkPath) {
    Write-Host "Removing broken NDK folder: $ndkPath"
    Remove-Item -Recurse -Force $ndkPath
    Write-Host "Done. Run: php native run"
} else {
    Write-Host "NDK folder not found at: $ndkPath"
    Write-Host "Listing existing NDK versions:"
    $ndkDir = "$env:LOCALAPPDATA\Android\Sdk\ndk"
    if (Test-Path $ndkDir) {
        Get-ChildItem $ndkDir | ForEach-Object { Write-Host "  $($_.Name)" }
    }
}
