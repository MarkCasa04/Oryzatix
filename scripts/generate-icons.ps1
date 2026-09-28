Add-Type -AssemblyName System.Drawing
$sizes = @(192, 512)
foreach ($s in $sizes) {
    $bmp = New-Object System.Drawing.Bitmap($s, $s)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.Clear([System.Drawing.Color]::FromArgb(45, 106, 62))
    $brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)
    $fontSize = [int]($s / 4)
    $font = New-Object System.Drawing.Font('Arial', $fontSize, [System.Drawing.FontStyle]::Bold)
    $g.DrawString('O', $font, $brush, ($s / 3), ($s / 3))
    $g.Dispose()
    $out = "c:\xampp\htdocs\Gregorio_Alaissa\public\images\icons\icon-$s.png"
    $bmp.Save($out, [System.Drawing.Imaging.ImageFormat]::Png)
    $bmp.Dispose()
}
Write-Host "Icons created"
