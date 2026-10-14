Add-Type -AssemblyName System.Drawing
$file = "C:\Users\LENOVO\.gemini\antigravity\brain\893b5868-3241-48fd-b04c-d8055458ff7a\.user_uploaded\media_1791959562815_e9b7e474.png"
$bmp = [System.Drawing.Bitmap]::FromFile($file)
# Sample across the letter 'E' in MERCURY, which is around X=160 to 180, Y=100 to 120
for ($y = 100; $y -le 115; $y += 2) {
    $line = ""
    for ($x = 160; $x -le 180; $x += 2) {
        $p = $bmp.GetPixel($x, $y)
        if ($p.R -lt 50) { $line += "##" }
        elseif ($p.R -lt 150) { $line += "++" }
        elseif ($p.R -lt 240) { $line += ".." }
        else { $line += "  " }
    }
    Write-Output $line
}
$bmp.Dispose()
