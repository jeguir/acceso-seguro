$PluginFile = "acceso-seguro.php"
$Nombre = "acceso-seguro"

$Contenido = Get-Content $PluginFile -Raw

if ($Contenido -match "Version:\s*([0-9]+\.[0-9]+\.[0-9]+)") {
    $Version = $Matches[1]
} else {
    Write-Error "No se ha podido detectar la version en $PluginFile"
    exit 1
}

$Release = "release"

if (!(Test-Path $Release)) {
    New-Item -ItemType Directory -Path $Release | Out-Null
}

$Zip = "$Release\$Nombre-v$Version.zip"

if (Test-Path $Zip) {
    Remove-Item $Zip
}

git archive `
    --format=zip `
    --output=$Zip `
    --prefix="$Nombre/" `
    HEAD

if ($LASTEXITCODE -ne 0) {
    Write-Error "No se ha podido crear el ZIP."
    exit 1
}

Write-Host "ZIP creado correctamente: $Zip"