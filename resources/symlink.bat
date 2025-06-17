@echo off
mklink F:\LocalSites\dbands\app\public\env-all.php F:\Projetos\CtrlAltVerso\sites\dbands\dbwp\env-all.php
mklink F:\LocalSites\dbands\app\public\env-local.php F:\Projetos\CtrlAltVerso\sites\dbands\dbwp\env-local.php
mklink /d F:\LocalSites\dbands\app\public\wp-content\themes\dbands F:\Projetos\CtrlAltVerso\sites\dbands\dbwp\wp-content\themes\dbands
mklink /d F:\LocalSites\dbands\app\public\wp-content\uploads\covers F:\Projetos\CtrlAltVerso\sites\dbands\dbwp\wp-content\uploads\covers
mklink /d F:\LocalSites\dbands\app\public\wp-content\uploads\logos F:\Projetos\CtrlAltVerso\sites\dbands\dbwp\wp-content\uploads\logos

mklink /d F:\LocalSites\dbands\app\public\wp-content\plugins\dbands F:\Projetos\CtrlAltVerso\sites\dbands\dbwp\wp-content\plugins\dbands
mklink /d F:\LocalSites\dbands\app\public\wp-content\plugins\cavWP F:\Projetos\CtrlAltVerso\plugins\wordpress\cavWP
