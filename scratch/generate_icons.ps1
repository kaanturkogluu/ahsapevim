Add-Type -AssemblyName System.Drawing

$srcPath = "c:\xampp\htdocs\ahsapevim\public\ahsaplogo_org.png"
$img = [System.Drawing.Image]::FromFile($srcPath)

$sizes = @(192, 96, 48, 32, 16, 180)
foreach ($sz in $sizes) {
    $bmp = New-Object System.Drawing.Bitmap($sz, $sz)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    $g.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    $g.DrawImage($img, 0, 0, $sz, $sz)

    $fileName = if ($sz -eq 180) { "apple-touch-icon.png" } elseif ($sz -eq 48) { "favicon-48x48.png" } else { "favicon-${sz}x${sz}.png" }
    $outPath = "c:\xampp\htdocs\ahsapevim\public\$fileName"
    $bmp.Save($outPath, [System.Drawing.Imaging.ImageFormat]::Png)
    
    $g.Dispose()
    $bmp.Dispose()
    Write-Host "Created $fileName"
}

# Also create favicon.ico from 48x48 icon
$bmp48 = New-Object System.Drawing.Bitmap(48, 48)
$g48 = [System.Drawing.Graphics]::FromImage($bmp48)
$g48.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$g48.DrawImage($img, 0, 0, 48, 48)
$hIcon = $bmp48.GetHicon()
$icon = [System.Drawing.Icon]::FromHandle($hIcon)
$fs = New-Object System.IO.FileStream("c:\xampp\htdocs\ahsapevim\public\favicon.ico", [System.IO.FileMode]::Create)
$icon.Save($fs)
$fs.Close()
$icon.Dispose()
$g48.Dispose()
$bmp48.Dispose()
Write-Host "Created standard favicon.ico"

$img.Dispose()
Write-Host "All icons generated successfully!"
