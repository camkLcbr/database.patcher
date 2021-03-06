<?php

/*
 * Copyright (c) 2013 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */

require_once __DIR__.'/../../../autoload.php';

use Mouf\Actions\InstallUtils;
use Mouf\MoufManager;
use Mouf\Database\DBConnection\ConnectionInterface;

// Let's init Mouf
InstallUtils::init(InstallUtils::$INIT_APP);

$moufManager = MoufManager::getMoufManager();

// Let's create the table.
$dbConnection = $moufManager->get('dbalConnection');
/* @var $dbConnection ConnectionInterface */

$existingPatches = $moufManager->findInstances("Mouf\\Database\\Patcher\\DatabasePatch");
$dbConnectionDescriptor = $moufManager->getInstanceDescriptor('dbalConnection');
foreach($existingPatches as $existingPatche){
    $patchIntance = $moufManager->getInstanceDescriptor($existingPatche);
    $patchIntance->getProperty('dbalConnection')->setValue($dbConnectionDescriptor);
}
$moufManager->rewriteMouf();
$dbConnection->exec(file_get_contents(__DIR__.'/../database/create_patches_table.sql'));

// Finally, let's continue the install
InstallUtils::continueInstall();
