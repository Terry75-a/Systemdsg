<?php

use App\Controllers\TipoplanaddController;
use App\Models\TipoPlanModel;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;
use Config\App;

final class TipoPlanEditorTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $config = config(\Config\Database::class);
        $this->assertSame('SQLite3', $config->tests['DBDriver']);
        $this->assertSame(':memory:', $config->tests['database']);
        $db = \Config\Database::connect('tests');
        $table = $db->prefixTable('tipo_plan');
        $db->query("CREATE TABLE IF NOT EXISTS $table (id_tipo_plan INTEGER PRIMARY KEY AUTOINCREMENT, nombre_tipo VARCHAR(50) NOT NULL)");
        $db->table('tipo_plan')->emptyTable();
    }

    private function guardar(array $post): Response
    {
        $config = new App();
        $request = new IncomingRequest($config, new URI('http://localhost/tipo_plan/guardar'), null, new UserAgent());
        $request->setMethod('POST');
        $request->setHeader('X-Requested-With', 'XMLHttpRequest');
        $request->setGlobal('post', $post);
        $controller = new TipoplanaddController();
        $controller->initController($request, new Response($config), service('logger'));
        return $controller->guardar();
    }

    public function testCreateThenEditKeepsTheSameRecord(): void
    {
        $created = $this->guardar(['nombre_tipo' => '  Mensual  ']);
        $this->assertSame(200, $created->getStatusCode());
        $this->assertTrue(json_decode($created->getBody(), true)['success']);
        $model = new TipoPlanModel();
        $row = $model->first();
        $this->assertSame('Mensual', $row->nombre_tipo);
        $updated = $this->guardar(['id_tipo_plan' => $row->id_tipo_plan, 'nombre_tipo' => 'Anual']);
        $this->assertSame(200, $updated->getStatusCode());
        $this->assertSame('Anual', $model->find($row->id_tipo_plan)->nombre_tipo);
        $this->assertSame(1, $model->countAllResults());
    }

    public function testInvalidNamesAndIdsCannotWrite(): void
    {
        foreach ([['nombre_tipo' => '   '], ['nombre_tipo' => str_repeat('a', 51)],
            ['nombre_tipo' => ['incorrecto']], ['nombre_tipo' => 'Mensual', 'id_tipo_plan' => 'abc'],
            ['nombre_tipo' => 'Mensual', 'id_tipo_plan' => ['1']]] as $data) {
            $this->assertSame(422, $this->guardar($data)->getStatusCode());
        }
        $this->assertSame(0, (new TipoPlanModel())->countAllResults());
    }

    public function testMissingRecordIsNotInsertedAsNew(): void
    {
        $this->assertSame(404, $this->guardar(['nombre_tipo' => 'Anual', 'id_tipo_plan' => '9999'])->getStatusCode());
        $this->assertSame(0, (new TipoPlanModel())->countAllResults());
    }
}
