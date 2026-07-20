<?php

use App\Models\CompteOperateurModel;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;

final class MobileMoneyFlowTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $db = db_connect('tests');
        $db->query('DROP TABLE IF EXISTS users');
        $db->query('DROP TABLE IF EXISTS compte_operateur');

        $db->query('CREATE TABLE users (id_user INTEGER PRIMARY KEY AUTOINCREMENT, numero VARCHAR(20) NOT NULL UNIQUE, nom VARCHAR(50) NOT NULL, solde DECIMAL(10,2) NOT NULL DEFAULT 0, pin_transaction VARCHAR(255) NOT NULL, date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
        $db->query('CREATE TABLE compte_operateur (id_compte INTEGER PRIMARY KEY AUTOINCREMENT, total_gain DECIMAL(10,2) DEFAULT 0)');

        $db->query("INSERT INTO users(numero, nom, solde, pin_transaction) VALUES ('0331234567', 'Michael', 50000, '1234')");
        $db->query('INSERT INTO compte_operateur(total_gain) VALUES (0)');
    }

    public function testUserModelCanReadAndUpdateBalance(): void
    {
        $userModel = new UserModel();

        $this->assertSame(50000.0, $userModel->getSolde(1));
        $this->assertTrue($userModel->updateSolde(1, 12500));
        $this->assertSame(12500.0, $userModel->getSolde(1));
    }

    public function testOperatorGainCanBeAccumulated(): void
    {
        $gainModel = new CompteOperateurModel();

        $this->assertTrue($gainModel->ajouterGain(12.5));
        $this->assertSame(12.5, $gainModel->getGain());
    }
}
