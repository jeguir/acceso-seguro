$PluginFile = "acceso-seguro.php"
$Nombre = "acceso-seguro"

$Contenido = Get-Content $PluginFile -Raw

if ($Contenido -match "Version:\s*([0-9]+\.[0-9]+\.[0-9]+)") {
    $Version = $Matches[1]
} else {
    Write-Error "No se ha podido detectar la version en $PluginFile"
    exit 1
}

$Dist = "dist"
$Zip = "$Dist\$Nombre-$Version.zip"

if (!(Test-Path $Dist)) {
    New-Item -ItemType Directory -Path $Dist | Out-Null
}

if (Test-Path $Zip) {
    Remove-Item $Zip
}

git archive `
    --format=zip `
    --output=$Zip `
    --prefix="$Nombre/" `
    HEAD

Write-Host "ZIP creado: $Zip"