<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Keyword;

class ImportKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:keywords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Keywords from Old BTS and add new words';

    protected $arr_keywords_import = [
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AC4CFF0936',
            'theme_id' => 6,
            'name' => 'Abastecimento de água'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A7156B7CE3',
            'theme_id' => 4,
            'name' => 'Acesso e distribuição de energia'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B1A8C21597',
            'theme_id' => 3,
            'name' => 'Acuidade visual'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AEEE230EDD',
            'theme_id' => 3,
            'name' => 'Agronegócio'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F079F63D26B2C',
            'theme_id' => 7,
            'name' => 'Alimentação escolar'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A0FB246F00',
            'theme_id' => 1,
            'name' => 'Analfabetismo'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AC812109CC',
            'theme_id' => 6,
            'name' => 'Armazenamento de água'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AF212C0F6B',
            'theme_id' => 3,
            'name' => 'Artesanato'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AF65460FF6',
            'theme_id' => 3,
            'name' => 'Aumento da renda familiar'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A9D6B80324',
            'theme_id' => 5,
            'name' => 'Biodesenvolvimento'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07ACB42C0A64',
            'theme_id' => 6,
            'name' => 'Bombeamento de água'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07ACEC660AFD',
            'theme_id' => 6,
            'name' => 'Captação de água'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AA16FC03B7',
            'theme_id' => 5,
            'name' => 'Coleta seletiva'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B1E8F71622',
            'theme_id' => 2,
            'name' => 'Combate à violência doméstica'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AF9D7C1082',
            'theme_id' => 3,
            'name' => 'Comercialização de produtos'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A132136F8B',
            'theme_id' => 1,
            'name' => 'Conscientização ambiental'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A16DB4701D',
            'theme_id' => 1,
            'name' => 'Conscientização política'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AA53820447',
            'theme_id' => 5,
            'name' => 'Controle ambiental'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B21F2A16B4',
            'theme_id' => 2,
            'name' => 'Controle de natalidade'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AFDD8F110D',
            'theme_id' => 3,
            'name' => 'Cooperativismo'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A1A30070A8',
            'theme_id' => 1,
            'name' => 'Cursos preparatórios para o vestibular'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A1DDCF7134',
            'theme_id' => 1,
            'name' => 'Cursos profissionalizantes'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A211A371BF',
            'theme_id' => 1,
            'name' => 'Defasagem escolar'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B253C6173F',
            'theme_id' => 2,
            'name' => 'Dependência química'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A24576724A',
            'theme_id' => 1,
            'name' => 'Desenvolvimento cognitivo e lingüístico'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A8509D7FCC',
            'theme_id' => 8,
            'name' => 'Sistemas construtivos'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B01A55119F',
            'theme_id' => 3,
            'name' => 'Desenvolvimento sustentável'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B289CB17CA',
            'theme_id' => 2,
            'name' => 'Desnutrição'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AA8C2C04D9',
            'theme_id' => 5,
            'name' => 'Recuperação ambiental'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AD4C070B90',
            'theme_id' => 6,
            'name' => 'Dessalinização'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07ADEC530C1E',
            'theme_id' => 6,
            'name' => 'Distribuição de água'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B2C30B1858',
            'theme_id' => 2,
            'name' => 'Doenças cardíacas'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B2F7C318E3',
            'theme_id' => 2,
            'name' => 'Doenças congênitas'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B32D99196F',
            'theme_id' => 2,
            'name' => 'Doenças contagiosas'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B363BD19FA',
            'theme_id' => 2,
            'name' => 'Doenças hidrotransmissíveis'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B3953E1A8B',
            'theme_id' => 2,
            'name' => 'Doenças hospitalares'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B3CAF51B17',
            'theme_id' => 2,
            'name' => 'Doenças infecciosas'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B4144D1BA3',
            'theme_id' => 2,
            'name' => 'Doenças oncológicas'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B450C71C2E',
            'theme_id' => 2,
            'name' => 'Doenças sexualmente transmissíveis'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A75CC67D6E',
            'theme_id' => 4,
            'name' => 'Economia de energia'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A29A1F72D8',
            'theme_id' => 1,
            'name' => 'Educação no trânsito'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A2D3CA7364',
            'theme_id' => 1,
            'name' => 'Educação sexual'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A3298173EF',
            'theme_id' => 1,
            'name' => 'Evasão escolar'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A39A217493',
            'theme_id' => 1,
            'name' => 'Exploração infantil'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A3D0FB752C',
            'theme_id' => 1,
            'name' => 'Exploração sexual'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B486921CBB',
            'theme_id' => 2,
            'name' => 'Fitoterapia'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A7904B7E07',
            'theme_id' => 4,
            'name' => 'Fontes alternativas'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A7C5E47EA5',
            'theme_id' => 4,
            'name' => 'Fontes renováveis'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AAC3B60564',
            'theme_id' => 5,
            'name' => 'Formação de agentes ambientais'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A8094D7F30',
            'theme_id' => 4,
            'name' => 'Geração de energia'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B05B4F122A',
            'theme_id' => 3,
            'name' => 'Geração de trabalho e renda'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AC1AF908AB',
            'theme_id' => 6,
            'name' => 'Gestão de água'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A8E2D000ED',
            'theme_id' => 8,
            'name' => 'Habitações populares'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F079F9F586BB9',
            'theme_id' => 7,
            'name' => 'Higienização de alimentos'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B4BBC71D47',
            'theme_id' => 2,
            'name' => 'Homeopatia'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A40FC475BE',
            'theme_id' => 1,
            'name' => 'Inclusão cultural na escola'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A446CB7649',
            'theme_id' => 1,
            'name' => 'Inclusão digital'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B096EA12B6',
            'theme_id' => 2,
            'name' => 'Inclusão produtiva de PCD'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A4916B76D7',
            'theme_id' => 1,
            'name' => 'Inclusão social de pessoa com deficiência'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A506F977F4',
            'theme_id' => 1,
            'name' => 'Interação escola e comunidade'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AE204D0CB0',
            'theme_id' => 6,
            'name' => 'Irrigação'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B4F8001DD2',
            'theme_id' => 2,
            'name' => 'Medicina alternativa'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A544D6787F',
            'theme_id' => 1,
            'name' => 'Melhoria da qualidade de ensino'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B0C95D1341',
            'theme_id' => 3,
            'name' => 'Microcrédito'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B52EB01E5E',
            'theme_id' => 2,
            'name' => 'Mortalidade infantil'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B56BBE1EE9',
            'theme_id' => 2,
            'name' => 'Mortalidade neonatal'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A5799A790B',
            'theme_id' => 1,
            'name' => 'Multi-repetência'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A5C0427996',
            'theme_id' => 1,
            'name' => 'Oficinas de arte'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A5F6167A24',
            'theme_id' => 1,
            'name' => 'Orientação social'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AAFE7105F0',
            'theme_id' => 5,
            'name' => 'Preservação ambiental'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A915C3017F',
            'theme_id' => 8,
            'name' => 'Prevenção contra deslizamentos'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F079FD8686C44',
            'theme_id' => 7,
            'name' => 'Produção de alimentos'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A018126CD0',
            'theme_id' => 7,
            'name' => 'Produção orgânica'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A638727AAF',
            'theme_id' => 1,
            'name' => 'Promoção da leitura'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B0FEBD13D1',
            'theme_id' => 3,
            'name' => 'Qualificação ou capacitação profissional'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AE50AC0D3B',
            'theme_id' => 6,
            'name' => 'Racionalização da água'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A055386D5B',
            'theme_id' => 7,
            'name' => 'Reaproveitamento alimentar'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AB3476067B',
            'theme_id' => 5,
            'name' => 'Reciclagem'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B131D71469',
            'theme_id' => 3,
            'name' => 'Reciclagem de lixo'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A66EB27B3B',
            'theme_id' => 1,
            'name' => 'Reciclagem de professores'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B5A4581F81',
            'theme_id' => 2,
            'name' => 'Violência contra a mulher'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AB66DE0706',
            'theme_id' => 5,
            'name' => 'Recuperação do solo'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A08DC46DE9',
            'theme_id' => 7,
            'name' => 'Redução do uso de agrotóxicos'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AB9B830794',
            'theme_id' => 5,
            'name' => 'Reflorestamento'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A6A4057BC6',
            'theme_id' => 1,
            'name' => 'Resgate/preservação de culturas'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07ABE19B081F',
            'theme_id' => 5,
            'name' => 'Resíduos sólidos'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AE84710DC6',
            'theme_id' => 6,
            'name' => 'Saneamento'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B5D8E62019',
            'theme_id' => 2,
            'name' => 'Saúde bucal'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A0C5CB6E74',
            'theme_id' => 7,
            'name' => 'Segurança alimentar'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B60BC520B1',
            'theme_id' => 2,
            'name' => 'Trabalho com gestantes'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07AEBBD40E52',
            'theme_id' => 6,
            'name' => 'Purificação da água'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B167331501',
            'theme_id' => 3,
            'name' => 'Turismo'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A6DB4B7C54',
            'theme_id' => 1,
            'name' => 'Mídias digitais no ensino'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A94828020A',
            'theme_id' => 8,
            'name' => 'Construção alternativa'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07A98A180298',
            'theme_id' => 8,
            'name' => 'Construção sustentável'
        ],
        [
            'cod_lumis' => '8AE389DB2F072B55012F07B641812144',
            'theme_id' => 2,
            'name' => 'Zoonoses'
        ],[
            'name' => 'Alimentação saudável',
            'theme_id' => 7
        ],
        [
            'name' => 'Alimentação natural',
            'theme_id' => 7
        ],
        [
            'name' => 'Plantas alimentícias não convencionais',
            'theme_id' => 7
        ],
        [
            'name' => 'Desperdício de alimentos',
            'theme_id' => 7
        ],
        [
            'name' => 'Educação patrimonial',
            'theme_id' => 1
        ],
        [
            'name' => 'Organização social',
            'theme_id' => 1
        ],
        [
            'name' => 'Inclusão artística na escola',
            'theme_id' => 1
        ],
        [
            'name' => 'Recuperação escolar',
            'theme_id' => 1
        ],
        [
            'name' => 'Orientação profissional',
            'theme_id' => 1
        ],
        [
            'name' => 'Distribuição de energia',
            'theme_id' => 4
        ],
        [
            'name' => 'Energias alternativas',
            'theme_id' => 4
        ],
        [
            'name' => 'Energia solar',
            'theme_id' => 4
        ],
        [
            'name' => 'Energia eólica',
            'theme_id' => 4
        ],
        [
            'name' => 'Biocombustível',
            'theme_id' => 4
        ],
        [
            'name' => 'Organização comunitária',
            'theme_id' => 8
        ],
        [
            'name' => 'Gestão patrimonial',
            'theme_id' => 8
        ],
        [
            'name' => 'Construção verde',
            'theme_id' => 8
        ],
        [
            'name' => 'Manutenção de construções',
            'theme_id' => 8
        ],
        [
            'name' => 'Construção com materiais recicláveis',
            'theme_id' => 8
        ],
        [
            'name' => 'Construção com materiais sustentáveis',
            'theme_id' => 8
        ],
        [
            'name' => 'Construção com materiais alternativos',
            'theme_id' => 8
        ],
        [
            'name' => 'Reutilização de resíduos',
            'theme_id' => 5
        ],
        [
            'name' => 'Poluição',
            'theme_id' => 5
        ],
        [
            'name' => 'Consumo consciente',
            'theme_id' => 5
        ],
        [
            'name' => 'Desenvolvimento sustentável',
            'theme_id' => 5
        ],
        [
            'name' => 'Agricultura orgânica',
            'theme_id' => 5
        ],
        [
            'name' => 'Agroecologia',
            'theme_id' => 5
        ],
        [
            'name' => 'Permacultura',
            'theme_id' => 5
        ],
        [
            'name' => 'Agroextrativismo',
            'theme_id' => 5
        ],
        [
            'name' => 'Sistema agroflorestal',
            'theme_id' => 5
        ],
        [
            'name' => 'Manejo agrícola sustentável',
            'theme_id' => 5
        ],
        [
            'name' => 'Manejo florestal sustentável',
            'theme_id' => 5
        ],
        [
            'name' => 'Unidade de proteção ambiental',
            'theme_id' => 5
        ],
        [
            'name' => 'Unidade de conservação ambiental',
            'theme_id' => 5
        ],
        [
            'name' => 'Reserva extrativista',
            'theme_id' => 5
        ],
        [
            'name' => 'Reserva ambiental',
            'theme_id' => 5
        ],
        [
            'name' => 'Bioma amazônia',
            'theme_id' => 5
        ],
        [
            'name' => 'Bioma cerrado',
            'theme_id' => 5
        ],
        [
            'name' => 'Bioma caatinga',
            'theme_id' => 5
        ],
        [
            'name' => 'Bioma mata atlântica',
            'theme_id' => 5
        ],
        [
            'name' => 'Bioma pantanal',
            'theme_id' => 5
        ],
        [
            'name' => 'Bioma pampa',
            'theme_id' => 5
        ],
        [
            'name' => 'Amazônia legal',
            'theme_id' => 5
        ],
        [
            'name' => 'Tratamento da água',
            'theme_id' => 6
        ],
        [
            'name' => 'Doenças transmitidas por animais',
            'theme_id' => 2
        ],
        [
            'name' => 'Obsesidade',
            'theme_id' => 2
        ],
        [
            'name' => 'Obsesidade infantil',
            'theme_id' => 2
        ],
        [
            'name' => 'Combate à violência infantil',
            'theme_id' => 2
        ],
        [
            'name' => 'Combate à violência social',
            'theme_id' => 2
        ],
        [
            'name' => 'DST',
            'theme_id' => 2
        ],
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Run with keywords to save
        return false;
        foreach ($this->arr_keywords_import as $keyword_item) {
            Keyword::create($keyword_item);
        }
    }
}
