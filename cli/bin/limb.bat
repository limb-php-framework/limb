@echo off

rem *************************************************************
rem ** Limb CLI for Windows based systems
rem *************************************************************

rem This script will do the following:
rem - check for PHP_COMMAND env, if found, use it.
rem echo ------------------------------------------------------------------------
rem echo WARNING: Set environment var PHP_COMMAND to the location of your php.exe
rem echo          executable (e.g. C:\PHP\php.exe).  (assuming php.exe on PATH)
rem echo ------------------------------------------------------------------------

rem %~dp0 is expanded pathname of the current script under NT
set SCRIPT_DIR=%~dp0

if "%PHP_COMMAND%" == "" (
set PHP_COMMAND=php.exe
)

%PHP_COMMAND% -d html_errors=off -d open_basedir= -q "%SCRIPT_DIR%\limb" %1 %2 %3 %4 %5 %6 %7 %8 %9
