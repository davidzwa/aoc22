# powershell.exe -ExecutionPolicy ByPass -Command ".\aoc9.ps1"
# https://stackoverflow.com/questions/25730978/powershell-add-type-cannot-add-type-already-exist

$file = "aoc9.cs";
$prog = Add-Type -Path $file -PassThru;
# try { [Program] | Out-Null } catch { 
#     Write-Output "Adding reference to assembly"
#     Add-Type -Path $file -PassThru 
# }

$asd = [Program]::Main();