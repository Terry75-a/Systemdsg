@echo off
chcp 65001 > nul
echo ===================================================
echo Ejecutando Notificador de Vencimientos - DSG Peru
echo Fecha y hora: %date% %time%
echo ===================================================

:: Ruta exacta de tu proyecto en XAMPP
cd /d "C:\xampp\htdocs\project-dsgperu"

:: Ejecuta el comando Spark y muestra el resultado en pantalla
"C:\xampp\php\php.exe" spark notificar:vencimientos

echo.
echo ===================================================
echo Proceso terminado.
pause