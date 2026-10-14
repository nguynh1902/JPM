Add-Type -AssemblyName System.Drawing

function GetLogoHeight($path) {
    $bmp = [System.Drawing.Bitmap]::FromFile($path)
    # Find bounding box of dark pixels around the mercury logo (top right area)
    $minY = $bmp.Height
    $maxY = 0
    $minX = $bmp.Width
    $maxX = 0
    for ($y = 0; $y -lt $bmp.Height; $y++) {
        for ($x = [int]($bmp.Width * 0.65); $x -lt $bmp.Width; $x++) {
            $p = $bmp.GetPixel($x, $y)
            # detect black/dark gray letters
            if ($p.R -lt 100 -and $p.G -lt 100 -and $p.B -lt 100 -and $y -lt [int]($bmp.Height * 0.6)) {
                if ($y -lt $minY) { $minY = $y }
                if ($y -gt $maxY) { $maxY = $y }
                if ($x -lt $minX) { $minX = $x }
                if ($x -gt $maxX) { $maxX = $x }
            }
        }
    }
    $bmp.Dispose()
    return "X: [$minX, $maxX] Y: [$minY, $maxY], Height: $($maxY - $minY), Width: $($maxX - $minX)"
}

Write-Output "Image 1 Mercury:"
Write-Output (GetLogoHeight "C:\Users\LENOVO\.gemini\antigravity\brain\893b5868-3241-48fd-b04c-d8055458ff7a\.user_uploaded\media_1791878617225_a1b14b93.png")

Write-Output "Image 2 Mercury:"
function GetLogoHeight2($path) {
    $bmp = [System.Drawing.Bitmap]::FromFile($path)
    $minY = $bmp.Height
    $maxY = 0
    $minX = $bmp.Width
    $maxX = 0
    for ($y = 0; $y -lt $bmp.Height; $y++) {
        for ($x = 0; $x -lt $bmp.Width; $x++) {
            $p = $bmp.GetPixel($x, $y)
            if ($p.R -lt 100 -and $p.G -lt 100 -and $p.B -lt 100) {
                if ($y -lt $minY) { $minY = $y }
                if ($y -gt $maxY) { $maxY = $y }
                if ($x -lt $minX) { $minX = $x }
                if ($x -gt $maxX) { $maxX = $x }
            }
        }
    }
    $bmp.Dispose()
    return "X: [$minX, $maxX] Y: [$minY, $maxY], Height: $($maxY - $minY), Width: $($maxX - $minX)"
}
Write-Output (GetLogoHeight2 "C:\Users\LENOVO\.gemini\antigravity\brain\893b5868-3241-48fd-b04c-d8055458ff7a\.user_uploaded\media_1791959562815_e9b7e474.png")
