Add-Type -AssemblyName System.Drawing

$src = Join-Path $PSScriptRoot '..\public\icons\logogestistore.png'
$dir = Split-Path $src

function Resize-Icon($size) {
    $srcImg = [System.Drawing.Image]::FromFile($src)
    $bmp = New-Object System.Drawing.Bitmap $size, $size
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $g.DrawImage($srcImg, 0, 0, $size, $size)
    $out = Join-Path $dir "icon-$size.png"
    $bmp.Save($out, [System.Drawing.Imaging.ImageFormat]::Png)
    $g.Dispose()
    $bmp.Dispose()
    $srcImg.Dispose()
    Write-Host "Created $out"
}

Resize-Icon 192
Resize-Icon 512
