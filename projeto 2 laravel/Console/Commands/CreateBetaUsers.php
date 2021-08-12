<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\SendUserEmail;
use App\SocialTecnology;
use App\SocialTecnologyUser;
use App\User;

class CreateBetaUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:betausers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria usuários para o lançamento do Beta';

    protected $import_data_wave_1 = [
        [
            'nome' => 'Carlos Eduardo Rezende Werner',
            'email' => 'caseh.werner@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01312FC55793778C'
        ],

        [
            'nome' => 'PAULO WATARU MORIMITSU',
            'email' => 'paulowataru@cediter.org.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BDBD698DE7B85'
        ],

        [
            'nome' => 'Manuel da Cruz Cosme de Siqueira',
            'email' => 'asproc.associacao@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB30C3692F0130C3FBC08B1DF3'
        ],

        [
            'nome' => 'Maria Clara Guaraldo Notaroberto',
            'email' => 'clara.guaraldo@embrapa.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5465796E0887'
        ],

        [
            'nome' => 'roseli cordeiro eurich',
            'email' => 'iafturvo@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB2FE9C063012FFE16921376DE'
        ],

        [
            'nome' => 'Maria de Fatima de Medeiros Vieira',
            'email' => 'aesca.fatima@terra.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA0131437ACB3B26B7'
        ],

        [
            'nome' => 'Maria Leinad Vasconcelos Carbognin',
            'email' => 'leinadfbc@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801343E61C1EA4E72'
        ],

        [
            'nome' => 'Maria Leinad Vasconcelos Carbognin',
            'email' => 'leinadfbc@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CB11CB57918C4'
        ],

        [
            'nome' => 'Roland Ernst Vetter',
            'email' => 'revetter@inpa.gov.br ',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E01309AB78336487E'
        ],

        [
            'nome' => 'Marcio Domingos Carvalhal de Moura',
            'email' => 'carvalhalmarcio@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D01C700573B21'
        ],

        [
            'nome' => 'Milza Moreira Lana',
            'email' => 'milza.Lana@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB4C0F2A6A014C4807333C5D0D'
        ],

        [
            'nome' => 'RODRIGO RIBEIRO FRANCO VIEIRA',
            'email' => 'rodrigo.franco@codevasf.gov.br',
            'cod_tecnologia_lumis' => '2C908A915B67132A015B87B358E272AF'
        ],

        [
            'nome' => 'Regina Filipini',
            'email' => 'projeto1@aacc-ms.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2FBCD7A2012FD50EAC5E432D'
        ],

        [
            'nome' => 'Odair Balen',
            'email' => 'obalen@verdevida.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C20AEA79D59B6'
        ],

        [
            'nome' => 'Maria da Conceição do Nascimento Oliveira.',
            'email' => 'mariaseissa2009@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3EEDD4292BCF'
        ],

        [
            'nome' => 'Norival',
            'email' => 'n.oliveira@ios.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014DA0BDDE43729D'
        ],

        [
            'nome' => 'Claudia Bandeira',
            'email' => 'claudia.bandeira@acaoeducativa.org',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012FA2E902AD3134'
        ],

        [
            'nome' => 'Alfredo Kingo Oyama Homma',
            'email' => 'alfredo.homma@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3EB2F1D3013EC1DBF39532C6'
        ],

        [
            'nome' => 'José Antonio Leite de Queiroz',
            'email' => 'jose.queiroz@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DD9BD2D4D0AC8'
        ],

        [
            'nome' => 'Maria Helena Alcântara de Oliveira',
            'email' => 'assessoriapedagogica@apaedf.org.br',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CA754636D0323'
        ],

        [
            'nome' => 'Regina Amuri Varga',
            'email' => 'regina@actc.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B01319004752A539F'
        ],

        [
            'nome' => 'Celene A. Brito',
            'email' => 'celene.grin9@grin9.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3EE7F3E9013EEB7DACC46014'
        ],

        [
            'nome' => 'Juliana Andrea Oliveira Batista',
            'email' => 'juliana.andrea@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB32A71BA00132B59C7D31627B'
        ],

        [
            'nome' => 'ANA GESSICA MONTEIRO DE SOUSA',
            'email' => 'GEKINHA.ESTRELAS@GMAIL.COM',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CC0D954197EF5'
        ],

        [
            'nome' => 'Cleo Manhas',
            'email' => 'cleo@inesc.org.br',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B4977BCC953BD'
        ],

        [
            'nome' => 'Nicolau Priante Filho',
            'email' => 'nicolaupf@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C1341D52017DE'
        ],

        [
            'nome' => 'Heraldo Firmino',
            'email' => 'heraldo@doutoresdaalegria.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3EDA4903013EE661509A181F'
        ],

        [
            'nome' => 'Vinícius de Almeida Americo',
            'email' => 'vinicius@projetopescar.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014DBAD6D1EB7D22'
        ],

        [
            'nome' => 'Patricia Menezes Dutra',
            'email' => 'patricia@poloiguassu.org',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C83A12962113C'
        ],

        [
            'nome' => 'Álvaro Figueredo dos Santos',
            'email' => 'alvaro.santos@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB4DE97DC9014DF80FDD265F78'
        ],

        [
            'nome' => 'Franquismar Marciel de Souza',
            'email' => 'franquiagri@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB32873BD00132879B6E4F25F5'
        ],

        [
            'nome' => 'Franquismar Marciel de Souza',
            'email' => 'franquiagri@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801344852C772705B'
        ],

        [
            'nome' => 'MÁRCIA HORA ACIOLI',
            'email' => 'marcia@inesc.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F39C6771627B5'
        ],

        [
            'nome' => 'Manuel da Cruz Cosme de Siqueira / Ricardo Bernardes',
            'email' => 'asproc.associacao@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4DE97DC9014DF842FE1D01EA'
        ],

        [
            'nome' => 'Luiz Carlos Guilherme',
            'email' => 'luiz.guilherme@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3EA4C55C013EA56921D97587'
        ],

        [
            'nome' => 'Frans G. C. Pareyn',
            'email' => 'franspar@rocketmail.com',
            'cod_tecnologia_lumis' => '8AE389DB2FBCD7A2012FC1D2E0B9037B'
        ],

        [
            'nome' => 'Regina Vidigal Guarita',
            'email' => 'artedespertar@artedespertar.org.br',
            'cod_tecnologia_lumis' => '8AE389DB32BBF8A70132D04F78F30C99'
        ],

        [
            'nome' => 'Maria Inês Andreotti Pereira',
            'email' => 'ines@parceirosvoluntarios.org.br',
            'cod_tecnologia_lumis' => '8AE389DB304139560130475B10F45D20'
        ],

        [
            'nome' => 'Patricia Menezes Dutra',
            'email' => 'patricia@poloiguassu.org',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D684244E43318'
        ],

        [
            'nome' => 'MARLENE TAVEIRA CINTRA',
            'email' => 'SERVICOSOCIAL@ADEVIRP.COM.BR',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DD8802B9005BB'
        ],
        [
            'nome' => 'MÁRCIA HORA ACIOLI',
            'email' => 'marcia@inesc.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F097B9C012F12187A52629F'
        ],
        [
            'nome' => 'Maria Sonia Lopes da Silva',
            'email' => 'sonia.lopes@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3EDA4903013EE3C749A84220'
        ],
        [
            'nome' => 'Nelson de Jesus Lopes',
            'email' => 'efasemandela@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3E5CBAE9013E60AF06D9738F'
        ],
        [
            'nome' => 'Grace Claudia Gasparini',
            'email' => 'gracegasparini59@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB32873BD0013287F4FFB43EE1'
        ],
        [
            'nome' => 'Eliana Rocha Passos Tavares de Moraes',
            'email' => 'elianarochatavares@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB30F6714B0130FA66E6E0294B'
        ],
        [
            'nome' => 'Wilson Tadeu Lopes da Silva',
            'email' => 'cnpdia.chgeral@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB2F1D7DDE012F3068CF1128F0'
        ],
        [
            'nome' => 'Denise Gutierrez',
            'email' => 'dmdgutie@uol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB30F6714B0130F8D1EAB441A3'
        ],
        [
            'nome' => 'Maria Leinad Vasconcelos Carbognin',
            'email' => 'leinadfbc@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CBD258CE85C38'
        ],
        [
            'nome' => 'Gabriela Nasser',
            'email' => 'gabriela@inmed.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DE83972E37F7A'
        ],
        [
            'nome' => 'Geo Britto',
            'email' => 'geobritto@ctorio.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3E38D10D013E4C6252E31A9D'
        ],
    ];

    protected $import_data_wave_2 = [
        [
            'nome' => 'Maria Inês Toledo de Azevedo Carvalho',
            'email' => 'ines@gabriel.org.br',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E0130990306603E6C'
        ],

        [
            'nome' => 'Marizelia Gomes Costa',
            'email' => 'marizelia@institutoagronelli.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D44201808760A'
        ],

        [
            'nome' => 'MARIA ELCI ZERMA',
            'email' => 'irmaelci@asab.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3EC3C5A1013EC7884600242D'
        ],

        [
            'nome' => 'Julia Mascarello',
            'email' => 'julia@acaomoradia.org.br',
            'cod_tecnologia_lumis' => '2C908A915BE9FFCB015BED2AB037504D'
        ],

        [
            'nome' => 'Fábio Muller',
            'email' => 'fabiomuller@cieds.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3E5CBAE9013E65CECDFA79C3'
        ],

        [
            'nome' => 'Raquel Maria Gomes Andrade',
            'email' => 'r.gomes@osdm.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5FAD04960980'
        ],

        [
            'nome' => 'Maria Alice Campos Freire',
            'email' => 'aliceharmonica@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DDF7B10DD6B97'
        ],

        [
            'nome' => 'Thaís Yuri Tanaka de Almeida',
            'email' => 'coordenacao@grupovidabrasil.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30099BB30130227FBAE0232E'
        ],

        [
            'nome' => 'Paola Weiss Monti',
            'email' => 'paola.monti@fiergs.org.br',
            'cod_tecnologia_lumis' => '8AE389DB32873BD00132882587241C06'
        ],

        [
            'nome' => 'Galeno Amorim',
            'email' => 'galeno@observatoriodolivro.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C4FE412C372A6'
        ],

        [
            'nome' => 'Thais Vojvodic',
            'email' => 'tvojvodic@coca-cola.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D4E6FD3397B98'
        ],

        [
            'nome' => 'Sylvia Albernaz Machado do Carmo Guimarães',
            'email' => 'sylvia@vagalume.org.br',
            'cod_tecnologia_lumis' => '8AE389DB300404AE01300918CD6F6AE7'
        ],

        [
            'nome' => 'Cassio Franco Moreira',
            'email' => 'cassiofrancomoreira@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5CBA6365727E'
        ],

        [
            'nome' => 'Maria Siqueira Santos',
            'email' => 'centrodeestudoscp@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C4CF7B06A6211'
        ],

        [
            'nome' => 'Eder José Azevedo Ramos',
            'email' => 'ederjaramos@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3657ACC90430'
        ],

        [
            'nome' => 'Francisco Samonek',
            'email' => 'franciscosamonek@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB300404AE013004755C8B1D92'
        ],

        [
            'nome' => 'Danielle Carvalho Basto Quaresma',
            'email' => 'danielle@instituto-ronald.org.br',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C9C818F261D3F'
        ],

        [
            'nome' => 'BILLYSHELBY FEQUIS',
            'email' => 'billyshelby11@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3ED15BE3013ED16E77F532CE'
        ],

        [
            'nome' => 'Gilson Miranda do Nascimento',
            'email' => 'gilson@acaatinga.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DE79019CF11CA'
        ],

        [
            'nome' => 'Monica Zagallo Camargo',
            'email' => 'monica.zagallo@goldeletra.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CA4B1EF0E0400'
        ],

        [
            'nome' => 'Ana Cláudia Torres Gonçalves',
            'email' => 'ana.claudia@mamiraua.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8CFFAF21068C'
        ],

        [
            'nome' => 'TANIA VITAL DA SILVA GOMES',
            'email' => 'tania.anjugomes@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B5EBF4E025FCD'
        ],

        [
            'nome' => 'Maria Isabel Grassi Dittert',
            'email' => 'mabel@bomaluno.com.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012F9857028C31EE'
        ],

        [
            'nome' => 'Amalia Fischer',
            'email' => 'amalia@fundosocialelas.org',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01311FCFBDE0679B'
        ],

        [
            'nome' => 'Gilson Miranda do Nascimento',
            'email' => 'gilson@acaatinga.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F5DAD4A013F5F5CA8400E02'
        ],

        [
            'nome' => 'Ana Maria Domingues Luz',
            'email' => 'contato@institutogea.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014C95ABE3EB7D07'
        ],

        [
            'nome' => 'Perolina Cezar Crescencio',
            'email' => 'perolinacezar@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F66AA74013F6749E40D3149'
        ],

        [
            'nome' => 'Josineide Barbosa Malheiros',
            'email' => 'josi.malheiros@yahoo.com',
            'cod_tecnologia_lumis' => '8AE389DB32873BD0013287D7A6150540'
        ],

        [
            'nome' => 'Claudio Roberto Anholetto Junior',
            'email' => 'claudio@mamiraua.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015BFED4A8E96BF8'
        ],

        [
            'nome' => 'Vera Christina Leonelli',
            'email' => 'vera.leonelli@juspopuli.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012FB244F8E60E0E'
        ],

        [
            'nome' => 'Mariah Miranda Ramos de Oliveira',
            'email' => 'mariah@institutoalianca.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DCFD3E2051D49'
        ],

        [
            'nome' => 'Caroline Schio',
            'email' => 'caroschio@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5EA6C5200594'
        ],

        [
            'nome' => 'Francisco Virgilio Melo da Silva',
            'email' => 'f.virgiliomelo.s@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BD948133B1AA2'
        ],

        [
            'nome' => 'Viviane dos Santos Junqueira',
            'email' => 'viviane@instituto-ronald.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3EC3C5A1013EC7AFF02C32FE'
        ],

        [
            'nome' => 'Monica Zagallo Camargo',
            'email' => 'monica.zagallo@goldeletra.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3E7FD149013E84EC6D5F10BF'
        ],

        [
            'nome' => 'Graziela Aparecida Bedoian',
            'email' => 'anaclaudia@projetoquixote.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B0131765409C05CCD'
        ],

        [
            'nome' => 'OTONIEL BARBOZA GARCEZ JUNIOR',
            'email' => 'jotagarcez@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB30413956013042808A691B2B'
        ],

        [
            'nome' => 'Joanna Alves Dutra',
            'email' => 'joannadutra@rumonautico.org,br',
            'cod_tecnologia_lumis' => '8AE389DB3144575A013152B2E52B4FC3'
        ],

        [
            'nome' => 'ANTONIO BUARQUE DE LIMA JÚNIOR',
            'email' => 'buarquecampestreal@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C31C1D92A69CE'
        ],

        [
            'nome' => 'Fábio Cezar Aidar Beneduce',
            'email' => 'fabio@iteva.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D900D91977A5D'
        ],

        [
            'nome' => 'Angelique van Zeeland',
            'email' => 'angelique@fld.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3F66AA74013F671DF902548E'
        ],

        [
            'nome' => 'Helena Bonumá',
            'email' => 'helenabonuma2014@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C1D20E207379A'
        ],

        [
            'nome' => 'Helena Bonumá',
            'email' => 'helenabonuma2014@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB33E135A80134585DD19D2D46'
        ],

        [
            'nome' => 'Rita de Cássia Gonçalves Viana',
            'email' => 'comunica@ceadec.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8D81C5CE474A'
        ],

        [
            'nome' => 'Eliane Gonçalves de Andrade',
            'email' => 'elianegandrade@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D43967DC737BE'
        ],

        [
            'nome' => 'Gilson Miranda do Nascimento',
            'email' => 'gilson@acaatinga.org.br',
            'cod_tecnologia_lumis' => '8AE389DB321FF31F01326966DE407230'
        ],

        [
            'nome' => 'Sylvia Albernaz Machado do Carmo Guimarães',
            'email' => 'sylvia@vagalume.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30FB9B8301310087AE2F7691'
        ],

        [
            'nome' => 'Paula Blandy',
            'email' => 'paulablandy@imagemagica.org',
            'cod_tecnologia_lumis' => '8AE389DB32873BD0013297E485541045'
        ],

        [
            'nome' => 'Davila Corra',
            'email' => 'davila@mamiraua.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3144575A013153771E4347BB'
        ],

        [
            'nome' => 'MARIA ELIZABETE PIRES MARTINS',
            'email' => 'mepmbeta@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB304139560130475DAAE66032'
        ],

        [
            'nome' => 'Maria Inês Toledo de Azevedo Carvalho',
            'email' => 'ines@gabriel.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30C3692F0130C84D70052A3B'
        ],

        [
            'nome' => 'MARIA CONSUELDA DE OLIVEIRA',
            'email' => 'mcoliveira31@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915B67132A015B7D6382B2607B'
        ],
    ];

    protected $import_data_wave_3 = [
        [
            'nome' => 'Solange Paixao de Jesus Oliveira',
            'email' => 'soly.paixao@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B624782BA325F'
        ],
        [
            'nome' => 'Hans Dieter Temp',
            'email' => 'htemp@uol.com.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BE8FDDF2C7043'
        ],
        [
            'nome' => 'Rosana Cebalho Fernandes',
            'email' => 'administracao@enff.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C03A9D27E0C77'
        ],
        [
            'nome' => 'Aline Maldonado Locks',
            'email' => 'alina.locks@aliancadaterra.org',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C11B347177010'
        ],
        [
            'nome' => 'Evandro Negrão',
            'email' => 'evandro@dispersores.org',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C12CB79162E95'
        ],
        [
            'nome' => 'Aline Schiavolin Duarte',
            'email' => 'social.acesa@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C219924734A19'
        ],
        [
            'nome' => 'Denise Barbieri Biscotto',
            'email' => 'denisebbiscotto@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C377E97F870B9'
        ],
        [
            'nome' => 'Elenice de Oliveira Matos',
            'email' => 'elenice.matos@cvr.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3FF4B52B7EA9'
        ],
        [
            'nome' => 'Mahyra Costivelli',
            'email' => 'mahyra@fazendohistoria.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C45D3D15A4F73'
        ],
        [
            'nome' => 'José Rogaciano Siqueira de Oliveira',
            'email' => 'rogacianoo@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5AF61E6B2E87'
        ],
        [
            'nome' => 'Ramom Morato',
            'email' => 'ramom.morato@idesam.org.br',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C7F1A321D2A22'
        ],
        [
            'nome' => 'Ana Paula dos Reis Locatelli',
            'email' => 'anapaulalocatelli@crescernocampo.org.br',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CC1108D2B02E3'
        ],
        [
            'nome' => 'SOLANGE',
            'email' => 'institutoterra@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB2F097B9C012F129730D82E8A'
        ],
        [
            'nome' => 'Leonora Michelin Laboissière Mol',
            'email' => 'leonora@ateliedeideias.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F097B9C012F12A87D845EA0'
        ],
        [
            'nome' => 'Denise Barbieri Biscotto',
            'email' => 'denisebbiscotto@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB2F36CF49012F451206FE3853'
        ],
        [
            'nome' => 'Isabel Sampaio Penteado',
            'email' => 'isabel@fazendohistoria.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012FB6FA59AF088D'
        ],
        [
            'nome' => 'SIDNEI BATISTELA',
            'email' => 'sudotec@sudotec.org.br ',
            'cod_tecnologia_lumis' => '8AE389DB2FBCD7A2012FDF4CC5E52472'
        ],
        [
            'nome' => 'Tânia Márcia',
            'email' => 'informatica@lutherking.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3023D13F01302898A8CA3A1D'
        ],
        [
            'nome' => 'Anna Christina Nascimento',
            'email' => 'anna.nascimento@institutovotorantim.org.',
            'cod_tecnologia_lumis' => '8AE389DB3041395601304DCF99A96F49'
        ],
        [
            'nome' => 'Evandro Negro',
            'email' => 'evandro@dispersores.org',
            'cod_tecnologia_lumis' => '8AE389DB307ADFD301308A1AAF814317'
        ],
        [
            'nome' => 'Júlia Mascarello',
            'email' => 'julia@acaomoradia.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30B783670130B85D12423536'
        ],
        [
            'nome' => 'Dalva Mansur',
            'email' => 'dalvamansur@ipeds.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30B783670130B9C6004B0F7B'
        ],
        [
            'nome' => 'NAIR SPINELLI LAURIA',
            'email' => 'nairzinha@nairzinha.com.br',
            'cod_tecnologia_lumis' => '8AE389DB30FB9B8301310AF7F8967A9E'
        ],
        [
            'nome' => 'Helena C. Vieira',
            'email' => 'helena@aliancaempreendedora.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA013143818D1D4A44'
        ],
        [
            'nome' => 'Ricardo Madeira',
            'email' => 'ric.mad@uol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B013167609863129F'
        ],
        [
            'nome' => 'Francisco Eudasio Alves da Silva',
            'email' => 'eudasiopaju@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB31BF62120131CEB25A0131DB'
        ],
        [
            'nome' => 'Alessandra da Cunha Peixoto',
            'email' => 'alessandra@institutounimedbh.com.br',
            'cod_tecnologia_lumis' => '8AE389DB32873BD001328C8B438479CD'
        ],
        [
            'nome' => 'Tatiana Carvalho',
            'email' => 'tatianacarvalho@redecidada.org.br',
            'cod_tecnologia_lumis' => '8AE389DB32873BD001328D5F3E0A20E6'
        ],
        [
            'nome' => 'André Folganes Franco',
            'email' => 'andre@redeinteracao.org.br',
            'cod_tecnologia_lumis' => '8AE389DB32BBF8A70132D01CB8BD017E'
        ],
        [
            'nome' => 'erika foureaux',
            'email' => 'erikafoureaux@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801343DF706677173'
        ],
        [
            'nome' => 'Maria Margareth Lins Rossal',
            'email' => 'rossal@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801344877C9333F3A'
        ],
        [
            'nome' => 'Augustin T. Wolzs',
            'email' => 'info@sociedadedosol.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3746AA6401379407BA0B2373'
        ],
        [
            'nome' => 'FRANCISCO EDSON BARRETO DE MEDEIROS',
            'email' => 'acci.icapui.2004@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3D837807013DA84B7D011A97'
        ],
        [
            'nome' => 'Saulo Faria Almeida Barretto',
            'email' => 'saulo@ipti.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3D837807013DBDF7B47A5B78'
        ],
        [
            'nome' => 'Maria Solimar da Silva Abreu',
            'email' => 'silimar@iespes.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB3E6A77BA013E6B7D784B0F32'
        ],
        [
            'nome' => 'ERIC JORGE SAWYER',
            'email' => 'eric@iabs.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3E6C051A013E7A703D000F20'
        ],
        [
            'nome' => 'Lenora Mendes',
            'email' => 'lenoramendes@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3E7FD149013E84A36CFD2EF0'
        ],
        [
            'nome' => 'Luiz Carlos Guilherme',
            'email' => 'luiz.guilherme@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3E94513A013E9E504CB06788'
        ],
        [
            'nome' => 'Francisco Elton de Macedo',
            'email' => 'assessoriacasaapis@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EE7F3E9013EEBA172F546FC'
        ],
        [
            'nome' => 'GISLEIDE DO CARMO OLIVEIRA CARNEIRO',
            'email' => 'gisleide@moc.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F2F4B67013F330B5BDA385B'
        ],
        [
            'nome' => 'Natalia Frota Goyanna',
            'email' => 'nataliagoyanna@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F3F0E310B31AD'
        ],
        [
            'nome' => 'Liliane Lacerda',
            'email' => 'iasb@iasb.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F5333E6592993'
        ],
        [
            'nome' => 'Maria Socorro Silva',
            'email' => 'acb.crato@superig.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3F541F5B013F57A3FF871538'
        ],
        [
            'nome' => 'Erica Sachhi Zanotti',
            'email' => 'erica@consuladodamulher.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CB96911636B17'
        ],
        [
            'nome' => 'Quionei de Araujo Silva',
            'email' => 'quionei@cfr.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4CC923FB014CCCF822270B67'
        ],
        [
            'nome' => 'Carlos Eduardo Falcão Luna',
            'email' => 'carloslunna@disroot.org',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D38F4FFAE7548'
        ],
        [
            'nome' => 'Geisiane Teixeira',
            'email' => 'varalagencia@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D82598B5F0F41'
        ],
        [
            'nome' => 'Carlos Augusto Rodrigues de Sena',
            'email' => 'carlosarsena@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8C1525BE756E'
        ],
        [
            'nome' => 'Vania Neu',
            'email' => 'bioneu@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014DA11128DC41FF'
        ],
        [
            'nome' => 'José Alejandro Garcia Prado',
            'email' => 'alejandro@seag.es.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB4DF8C4EF014DF8CAA86923D0'
        ],
    ];

    protected $import_data_wave_4 = [
        [
            'nome' => 'Karine Gomes Queiroz',
            'email' => 'karine.queiroz@unila.edu.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C60D0ACAA5305'
        ],
        [
            'nome' => 'Igor Viana Brandi',
            'email' => 'ibrandi@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C4630409A48D9'
        ],
        [
            'nome' => 'Francisca Izabel Castro Porto',
            'email' => 'izabelporto@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BA50C87D60689'
        ],
        [
            'nome' => 'Antonio Feres Neto',
            'email' => 'a.feres@sepg.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8AAB9CF845D2'
        ],
        [
            'nome' => 'Jéssica de Almeida Lima',
            'email' => 'jessica.lima@pti.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4CC923FB014CE11082BE6FF0'
        ],
        [
            'nome' => 'Marcelo Silva Santos',
            'email' => 'branco@f7.com.br',
            'cod_tecnologia_lumis' => '8AE389DB30099BB3013022CF5AD326F5'
        ],
        [
            'nome' => 'Alice de Oliveira Almeida',
            'email' => 'alice-arte@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015B91989C1F63D8'
        ],
        [
            'nome' => 'Rosângela Tigre da Silva',
            'email' => 'asbcrose@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915B67132A015B869138451CC6'
        ],
        [
            'nome' => 'Priscila Batista da Silva',
            'email' => 'priscilabatista2@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015CA5496C536BC7'
        ],
        [
            'nome' => 'FLAVIA MOTA',
            'email' => 'flavia@cpcd.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013423FA582710E7'
        ],
        [
            'nome' => 'José Dias Campos',
            'email' => 'cepfsjd@bol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB2F36CF49012F49AF5D181E06'
        ],
        [
            'nome' => 'Fábio Caetano Machado',
            'email' => 'caetano.cataguases@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C0408997D3AE1'
        ],
        [
            'nome' => 'Celio Maia',
            'email' => 'verdanyperfumes@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01313DD9F39274CF'
        ],
        [
            'nome' => 'José Dias Campos',
            'email' => 'cepfsjd@bol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4C0F2A6A014C755070215265'
        ],
        [
            'nome' => 'Gabriel Cezar Carneiro dos Santos',
            'email' => 'gabrielc321@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01312119A04B1C42'
        ],
        [
            'nome' => 'IRLANIA DE ALENCAR FERNANDES',
            'email' => 'lana@caatinga.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3065260B0130662D58E47476'
        ],
        [
            'nome' => 'Kelly Christine Barbosa do Valle Lopes',
            'email' => 'kellyc@ios.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012FB29563354F5C'
        ],
        [
            'nome' => 'Maria do Socorro de Jesus',
            'email' => 'socorro.jesus@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801343E24D89445DB'
        ],
        [
            'nome' => 'Maria Beatriz Vedovello Bimbati',
            'email' => 'rasfonoaudiologia@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CC0D3023F5E65'
        ],
        [
            'nome' => 'Dalcio Marinho Gonçalves e Eliana Sousa Silva',
            'email' => 'dalcio@redesdamare.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D925D37C00774'
        ],
        [
            'nome' => 'Erlon Marcelino Bispo',
            'email' => 'erlonbispo@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C36CEEE802F71'
        ],
        [
            'nome' => 'Tatiana Cardoso',
            'email' => 'tatyana_jp@yahoo.comm.br',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C9DCDD74E5175'
        ],
        [
            'nome' => 'Beatriz Lemos Santiago',
            'email' => 'beatrizlemos.s@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4CC923FB014CDC6DFB717326'
        ],
        [
            'nome' => 'Wagner Santos',
            'email' => 'wgn_sp@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014DC41FFB98365C'
        ],
        [
            'nome' => 'Adriana Silva',
            'email' => 'adrianasilva@ipccic.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C2195A91B44C8'
        ],
        [
            'nome' => 'Marcia Benicio da Silva Cipriano',
            'email' => 'marciabenicioc@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CB76AC0CC600F'
        ],
        [
            'nome' => 'CRISTINA ANCONA LOPEZ',
            'email' => 'TITA@DOUTORESDASAGUAS.ORG.BR',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5A6DBBF36604'
        ],
        [
            'nome' => 'Anna Keylla Alves Maia',
            'email' => 'keyllagacc@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3A7423262FF4'
        ],
        [
            'nome' => 'Monica Rabelo de Freitas Moreira',
            'email' => 'diretoria@portaliep.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5E5E9E100EAF'
        ],
        [
            'nome' => 'LETÍCIA MACHADO DOS SANTOS',
            'email' => 'leticia.machado@educacao.ba.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB3F10C3B0013F15542FF45E47'
        ],
        [
            'nome' => 'Camila Andrade Vaz',
            'email' => 'camilavaz@escoladenoticias.org',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C56663F6B23DF'
        ],
        [
            'nome' => 'Patricia Azevedo',
            'email' => 'patriciaazevedo@agenciadobem.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01312A2A77D84850'
        ],
        [
            'nome' => 'RANIERE PONTES',
            'email' => 'RANIERE_PONTES@WVI.ORG',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CC782BAF22379'
        ],
        [
            'nome' => 'Jéssica de Almeida Lima',
            'email' => 'jessica.lima@pti.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D90C4AB196AF1'
        ],
        [
            'nome' => 'João Carlos Farias',
            'email' => 'doutoragua@doutoragua.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D6DE2A5576EE7'
        ],
        [
            'nome' => 'Júlia Mascarello',
            'email' => 'julia@acaomoradia.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30B783670130B85D12423536'
        ],
        [
            'nome' => 'Kátia Rejane Holanda Lopes',
            'email' => 'jane@caatinga.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3B497A8540AF'
        ],
        [
            'nome' => 'Paulo Mario Machado Araujo',
            'email' => 'paubaumma@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3144575A013152B5488E6519'
        ],
        [
            'nome' => 'Francisvaldo Amaral Roza',
            'email' => 'francisvaldo@cfri.org.br',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C877690416EF1'
        ],
        [
            'nome' => 'ANTONIO HENRIQUE PEREIRA',
            'email' => 'antonio.henrique@emater.mg.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB3E7FD149013E895698FD4981'
        ],
        [
            'nome' => 'FERNANDO HENRIQUE VIDAL LAGE',
            'email' => 'nando_lage@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A915B67132A015B8C8DB024698A'
        ],
        [
            'nome' => 'José Corintho Araújo Costa',
            'email' => 'jcorintho@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3065260B01306B042A0E191B'
        ],
        [
            'nome' => 'José Corintho Araújo Costa',
            'email' => 'jcorintho@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EE680E6013EE746C54E3860'
        ],
        [
            'nome' => 'Philippe Magno',
            'email' => 'philippe@institutohandsfree.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5F050F7378A9'
        ],
        [
            'nome' => 'Soraya El-Deir',
            'email' => 'sorayageldeir@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F6823FE013F68AD4A1009F8'
        ],
        [
            'nome' => 'Ênio França e Silva',
            'email' => 'enio.silva@dtr.ufrpe.br',
            'cod_tecnologia_lumis' => '8AE389DB3F6823FE013F68AD4A1009F8'
        ],
        [
            'nome' => 'José Dias Campos',
            'email' => 'cepfsjd@bol.com.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01695E93896014CA'
        ],
        [
            'nome' => 'Maria Cláudia Medeiros Dantas de Rubim Costa',
            'email' => 'mclaudiadantas@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3065260B013065C365664A1B'
        ],
        [
            'nome' => 'FLAVIA MOTA',
            'email' => 'flavia@cpcd.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012F9CFBC10D1E55'
        ],
        [
            'nome' => 'Maria Celuta Machado Viana',
            'email' => 'mcv@epamig.br',
            'cod_tecnologia_lumis' => '8AE389DB3ED15BE3013ED27F8CE427DE'
        ],
        [
            'nome' => 'ALTEMAR FELBERG',
            'email' => 'felberg_imt@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3596AA335364'
        ],
        [
            'nome' => 'Denise Silva',
            'email' => 'ipedi.diretoria@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5B33245F7232'
        ],
        [
            'nome' => 'Mariana Aleixo',
            'email' => 'marialeixo@redesdamare.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30DCA5150130E1F1E63F3E66'
        ],
        [
            'nome' => 'Rubens Ferronato',
            'email' => 'rubensferron@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB32029EBB013208162EF358B1'
        ],
        [
            'nome' => 'REJANE BRESSAN',
            'email' => 'rejanebressan@cidadejunior.org.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BD58668DD0EE0'
        ],
        [
            'nome' => 'Renata Cavalcanti',
            'email' => 'renata_cavalcanti2@wvi.org',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F443B10957700'
        ],
        [
            'nome' => 'José Eduardo Ferreira da Silva',
            'email' => 'jeduardo@powerline.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C31DF0AA321BF'
        ],
        [
            'nome' => 'Edgleison Rodrigues',
            'email' => 'edgleison_rodrigues@wvi.org',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01311A08BB9C5D26'
        ],
        [
            'nome' => 'Maria das Neves Caldas de Souza',
            'email' => 'redemulheresprodutorasdopajeu@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C1667C9086036'
        ],
        [
            'nome' => 'Raimundo Melo',
            'email' => 'cecop_rn@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CB0DD31BD57EC'
        ],
        [
            'nome' => 'Antonio Eduardo Kloc',
            'email' => 'eduardo.kloc@ifpr.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB4C0F2A6A014C291ED0C16D3C'
        ],
        [
            'nome' => 'Denise Curi',
            'email' => 'denicuri@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5FB81FB9084E'
        ],
        [
            'nome' => 'Jéssica de Almeida Lima',
            'email' => 'jessica.lima@pti.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3144575A01314DD06AF90537'
        ],
        [
            'nome' => 'Elizângela Lopes Lima',
            'email' => 'elizangelaapa500@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915B67132A015B8B9E255A3207'
        ],
        [
            'nome' => 'Tiago Manenti Martins',
            'email' => 'tiagoaquicultura@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BA10B81F36DBB'
        ],
        [
            'nome' => 'FLAVIA MOTA',
            'email' => 'flavia@cpcd.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013423D740DE50AD'
        ],
        [
            'nome' => 'Fabrício Monte Mendes',
            'email' => 'josilda.ribeiro@pirambudigital.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D1FFD11955BA9'
        ],
        [
            'nome' => 'Newton Barboza Campos',
            'email' => 'davi_ssenna@yahoo.com',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B2162CE6D17BE'
        ],
        [
            'nome' => 'Isadora Santos',
            'email' => 'isadora@designpossivel.org',
            'cod_tecnologia_lumis' => '8AE389DB3F66AA74013F66E2C48A38CF'
        ],
        [
            'nome' => 'Ivo Pons',
            'email' => 'ivopons@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F66AA74013F66E2C48A38CF'
        ],
        [
            'nome' => 'Felipe Souto Alves',
            'email' => 'felipesalves@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CC37273E74398'
        ],
        [
            'nome' => 'Jéssica de Almeida Lima',
            'email' => 'jessica.lima@pti.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014D9C3BBF156548'
        ],
        [
            'nome' => 'Mariana Monferdini',
            'email' => 'mariana@polis.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31538968013153C4E95302BA'
        ],
        [
            'nome' => 'André Luís Alves',
            'email' => 'andre@pactodasaguas.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4DF8C4EF014DF992DF423BF0'
        ],
        [
            'nome' => 'CLAUDIO LUCIO DA SILVA',
            'email' => 'CLAUDIOJUCA@HOTMAIL.COM',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801344836A7C73604'
        ],
        [
            'nome' => 'Antonio Adriano Batista',
            'email' => 'adriano@adel.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3D837807013DA19670D5760F'
        ],
        [
            'nome' => 'Claudia Bandeira',
            'email' => 'claudia.bandeira@acaoeducativa.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2FBCD7A2012FC18358FC6D4B'
        ],
        [
            'nome' => 'Cleiton Cerqueira Andrade',
            'email' => 'kleitonandrade2305@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5BE2AE2702CC'
        ],
        [
            'nome' => 'Jorge Luís de Paula',
            'email' => 'jluisdepaula20@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F5DAD4A013F5E9581440DB2'
        ],
        [
            'nome' => 'Eduardo Koqui',
            'email' => 'ekoqui@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CC14B12A92A09'
        ],
        [
            'nome' => 'Walmir Nogueira Moraes',
            'email' => 'walmir.nogueira@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4DE97DC9014DF8001EF20CF3'
        ],
        [
            'nome' => 'FERNANDA TUNES VILLANI',
            'email' => 'fernanda.villani@ifam.edu.br',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CB74B43C22F2C'
        ],
        [
            'nome' => 'Luciana Lopes Damasceno',
            'email' => 'luciana.damasceno@irmadulce.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012F98A4A42F170E'
        ],
        [
            'nome' => 'Odair Balen',
            'email' => 'obalen@verdevida.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3023D13F01303286B2774573'
        ],
        [
            'nome' => 'Fabíola Ribeiro R de Almeida',
            'email' => 'fabiola@povosdamata.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C65BF0B8F22F5'
        ],
        [
            'nome' => 'Francinaldo Ferreira de Lima',
            'email' => 'naldobio2004@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5FF2621B7A93'
        ],
        [
            'nome' => 'Lya Lena Garcia de Lacerda',
            'email' => 'projetoecobolsa@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F522786C44332'
        ],
        [
            'nome' => 'Andréa Jaeger Foresti',
            'email' => 'andreajforesti@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB2FBCD7A2012FD6989DFF75EF'
        ],
        [
            'nome' => 'Alan Santos Jacob',
            'email' => 'alan.abacateiro@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3FA0791F6072'
        ],
        [
            'nome' => 'Alessandro Ugolini',
            'email' => 'alessandro.ugolini@ucodep.org',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B63B101617A01'
        ],
        [
            'nome' => 'Ana Maria Bianchi',
            'email' => 'ana.bianchi@avsi.org',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BD349CEF43672'
        ],
        [
            'nome' => 'José Dias Campos',
            'email' => 'cepfsjd@bol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4C0F2A6A014C55B33DE1622A'
        ],
        [
            'nome' => 'Antonio Adriano Batista',
            'email' => 'adriano@adel.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CC85131633092'
        ],
        [
            'nome' => 'Daiany França Saldanha',
            'email' => 'daianyfranca@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3592B5964DAF'
        ],
        [
            'nome' => 'José Dias Campos',
            'email' => 'cepfsjd@bol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB2F1D7DDE012F31B525DC105A'
        ],
        [
            'nome' => 'Joanne Régis da Costa',
            'email' => 'joanne.regis@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB32873BD0013287EDB6BF262F'
        ],
        [
            'nome' => 'Antonivaldo de Sousa',
            'email' => 'assagrir1@bol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB32873BD0013287EDB6BF262F'
        ],
        [
            'nome' => 'Rejane Pacheco de Carvalho',
            'email' => 'rejane@reciclandosons.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3E7FD149013E8EB79195022F'
        ],
        [
            'nome' => 'Monica Rabelo de Freitas Moreira',
            'email' => 'diretoria@portaliep.com',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014D9B8F43640960'
        ],
        [
            'nome' => 'Francisca Valdelice Fialho',
            'email' => 'valdelice@gacc.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30FB9B830131198F730E00A8'
        ],
        [
            'nome' => 'Jane Christina Pereira',
            'email' => 'jane.christina@ifb.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D57E19E7247C4'
        ],
        [
            'nome' => 'Ana Paula Santiago Seixas Andrade',
            'email' => 'ana.seixas@ifb.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D57E19E7247C4'
        ],
        [
            'nome' => 'Ricardo Trento',
            'email' => 'trento@unicultura.com.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012FB1DF73F456E6'
        ],
        [
            'nome' => 'Jussara Maria Utsch',
            'email' => 'jussara.utsch@institutosustentar.net',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C1B905AA92C8C'
        ],
        [
            'nome' => 'Douglas Brian Trent',
            'email' => 'douglas.trent@institutosustentar.net',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C1B905AA92C8C'
        ],
        [
            'nome' => 'Fernanda Henrique Estevão',
            'email' => 'fernandahenriqueestevao@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8AE78D691ACC'
        ],
        [
            'nome' => 'Jéssica de Almeida Lima',
            'email' => 'jessica.lima@pti.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA0131428C0E7F5525'
        ],
    ];

    protected $import_data_wave_5 = [
        [
            'nome' => 'EDSON BILCHE GIROTTO',
            'email' => 'presidencia@daep.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DE24316315ED5'
        ],
        [
            'nome' => 'Madeline Abreu Monteiro',
            'email' => 'psi.edisca@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB31BF62120131D3DE63E86064'
        ],
        [
            'nome' => 'MURILO SÉRGIO DRUMMOND',
            'email' => 'murilosd.bee@gmail.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012F9CBA1D8A4533'
        ],
        [
            'nome' => 'Maria Sueli Fonseca Gonçalves',
            'email' => 'suelizinha@sme.prefeitura.sp.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DE88084BC0998'
        ],
        [
            'nome' => 'Carla Fagundes',
            'email' => 'carlarfagundes@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E01308E02960F55C5'
        ],
        [
            'nome' => 'Ivi Aliana Carlos Dantas',
            'email' => 'ivialiana@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8B0213E015B2'
        ],
        [
            'nome' => 'Ines Lourenço Augusto',
            'email' => 'msrsilva@uol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA013128D9979A4A68'
        ],
        [
            'nome' => 'Fernando Alberto Buzetto',
            'email' => 'fernando@floratiete.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3EB2F1D3013EB357D0E51C4A'
        ],
        [
            'nome' => 'HELMUT SCHNED',
            'email' => 'mobilizacao@projetoaxe.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30DCA5150130E69B788845E5'
        ],
        [
            'nome' => 'Edilene Pimentel',
            'email' => 'edilenepgomes@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CC1AC7C4054DE'
        ],
        [
            'nome' => 'Waneska Bonfim',
            'email' => 'waneska@diaconia.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D4E8C50656E0C'
        ],
        [
            'nome' => 'Waneska Bonfim',
            'email' => 'waneska@diaconia.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D243FCEBB3F21'
        ],
        [
            'nome' => 'José Alberto Caram de Souza Dias',
            'email' => 'jcaram@iac.sp.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB30DCA5150130E6AE66CA0327'
        ],
        [
            'nome' => 'ELCIO PEDRÃO',
            'email' => 'elcio@epagri.sc.gov.br',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B5855E37B6575'
        ],
        [
            'nome' => 'NORMA CARVALHO',
            'email' => 'nsscarvalho@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D0143429A2149'
        ],
        [
            'nome' => 'Maria Eneide Teixeira',
            'email' => 'eneideteixeira@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB307ADFD30130844E3D715F3A'
        ],
        [
            'nome' => 'Vera Lúcia Anastácio',
            'email' => 'veranastacio@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB307ADFD30130844E3D715F3A'
        ],
        [
            'nome' => 'André Azevedo Rocha',
            'email' => 'andre@irpaa.org',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014D9BA6D33757E7'
        ],
        [
            'nome' => 'Mirian Rose Rebello',
            'email' => 'mirian@fiocruz.br',
            'cod_tecnologia_lumis' => '8AE389DB3EE680E6013EE78AE97F6062'
        ],
        [
            'nome' => 'Mara Helena Saalfeld',
            'email' => 'msaalfeld@emater.tche.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BC1AE76B4187A'
        ],
        [
            'nome' => 'Lourdes Maria Staudt Dill',
            'email' => 'projeto@esperancacooesperanca.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA0131262BEEE95D07'
        ],
        [
            'nome' => 'José Artur de Barros Padilha',
            'email' => 'zearturpadilha@uol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DDD5B8D356143'
        ],
        [
            'nome' => 'Sofia Carminati Perinazzo',
            'email' => 'sofi_cp@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3EE680E6013EE75D15CF67AA'
        ],
        [
            'nome' => 'Marcina Maria Pessoa Coelho',
            'email' => 'marcina@fundacaomargaridaalves.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30099BB301300E9D0E3035AE'
        ],
        [
            'nome' => 'Francisco José Loureiro Marinho',
            'email' => 'chicohare@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015BEE643DB51B6F'
        ],
        [
            'nome' => 'Ivanete Bandeira Cardozo',
            'email' => 'ivanete@kaninde.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01311B0408225F09'
        ],
        [
            'nome' => 'Sandra Schafer da Silva',
            'email' => 'ssandrinhas@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3041395601304D5A91551D34'
        ],
        [
            'nome' => 'Luciana Paschoalin',
            'email' => 'educajovem@educandariorp.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5468CF0E20B8'
        ],
        [
            'nome' => 'Débora Porto Maciel da Silva',
            'email' => 'la@comec.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30AD3B910130AE4902B70277'
        ],
        [
            'nome' => 'Pierângeli Cristina Marim Aoki',
            'email' => 'pieraoki@incaper.es.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D9011BBEC0296'
        ],
        [
            'nome' => 'Prof Francisco Gödke',
            'email' => 'godke@utfpr.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB3065260B01306660FFFD662C'
        ],
        [
            'nome' => 'Sandro Rogério do Nascimento',
            'email' => 'sanroge@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BB751C3E67842'
        ],
        [
            'nome' => 'Valéria Gonalves Pssaro',
            'email' => 'valeriagonpassaro@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015BF3B82E9C3E7C'
        ],
        [
            'nome' => 'Carlos Alberto de Jesus Reis',
            'email' => 'cjesusreis@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EC3C5A1013ECCD4C82C56D4'
        ],
        [
            'nome' => 'Terezinha de Jesus Soares dos Santos',
            'email' => 'teca65@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB30099BB301300D9FDAF96451'
        ],
        [
            'nome' => 'Júlia Fernandes de Carvalho',
            'email' => 'jcarvalho@fastfooddapolitica.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5CD8E148493C'
        ],
        [
            'nome' => 'GUSTAVO NOGARA DOTTO',
            'email' => 'ctdbem@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C49B9158305D1'
        ],
        [
            'nome' => 'Cristina de Brito Ribeiro',
            'email' => 'cristina.ribeiro@abiorj.org',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C90DE2A4366B7'
        ],
        [
            'nome' => 'Francisco Xavier Sobrinho',
            'email' => 'permaculturacaicara@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D7D7CABF56991'
        ],
        [
            'nome' => 'Barbara Schmidt Rahmer',
            'email' => 'barbara@esquel.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F3A3CE8273BF8'
        ],
        [
            'nome' => 'Beth Callia',
            'email' => 'beth@formare.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30DCA5150130E679466004EA'
        ],
        [
            'nome' => 'Fernanda',
            'email' => 'fernanda@formare.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30DCA5150130E679466004EA'
        ],
        [
            'nome' => 'Daniela Tolfo',
            'email' => 'danielatolfo@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CC13A753B5647'
        ],
        [
            'nome' => 'Thomas Neves de Freitas',
            'email' => 'thomfreitas@eprocad.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C362706574E1D'
        ],
        [
            'nome' => 'Gustavo Henrique Gonzaga da Silva',
            'email' => 'gustavo@ufersa.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB4DE97DC9014DF239DC3765AE'
        ],
        [
            'nome' => 'Jorge Gonzaga de Oliveira',
            'email' => 'contato@aehda.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B01316739F0C64B78'
        ],
        [
            'nome' => 'Vicente de Paulo Santos de Oliveira',
            'email' => 'vicentepsoliveira@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB30DCA5150130E6EE234A62FF'
        ],
        [
            'nome' => 'Pierângeli Cristina Marim Aoki',
            'email' => 'pieraoki@incaper.es.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D963523FF269A'
        ],
        [
            'nome' => 'Cláudia Leal',
            'email' => 'claudia.leal@fpc.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3065260B013065D73CEA7E6B'
        ],
        [
            'nome' => 'Alexandre Henrique Bezerra Pires',
            'email' => 'alexandre@centrosabia.org.br',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E01309DE8832A1D25'
        ],
        [
            'nome' => 'Augusto José Savioli de Almeida Sampaio',
            'email' => 'asampaio@uel.br',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E01309E8B4E7F53BD'
        ],
        [
            'nome' => 'Flávia Neves de Oliveira Castro',
            'email' => 'eudisseflavia@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D20E46FF140A7'
        ],
        [
            'nome' => 'Dafne Henriques Spolti',
            'email' => 'dafne@amazonianativa.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D057BF48F688F'
        ],
        [
            'nome' => 'Paulo Belli Filho',
            'email' => 'paulo.belli@ufsc.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D0AC7577264A0'
        ],
        [
            'nome' => 'Eneida de Almeida Melo',
            'email' => 'soldoxingu@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3ED15BE3013ED2DFB9EC67BB'
        ],
        [
            'nome' => 'Vitor Hugo Hollas',
            'email' => 'vitor@capa.org.br',
            'cod_tecnologia_lumis' => '8AE389DB300404AE013008C72842156E'
        ],
        [
            'nome' => 'Felipe de Azevedo Silva Ribeiro',
            'email' => 'felipe@ufersa.edu.br',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B30E6AF767F11'
        ],
        [
            'nome' => 'Alexandre Henrique Bezerra Pires',
            'email' => 'alexandre@centrosabia.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C2248FCA66B43'
        ],
        [
            'nome' => 'Sandra Regina Albarello',
            'email' => 'sandrad@unijui.edu.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5A5EFC3B61FF'
        ],
        [
            'nome' => 'Luiz Carlos Pinto da Silva Filho',
            'email' => 'grid.ufrgs@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F3969BF6039A9'
        ],
        [
            'nome' => 'Flávia Cremonesi',
            'email' => 'ambiental@fundacaojulita.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014DB5C3F3136E81'
        ],
        [
            'nome' => 'Arnaldo Santos',
            'email' => 'arnaldo.santos@fundacaojari.org.br',
            'cod_tecnologia_lumis' => '8AE389DB307ADFD301308A4DFEA12DA8'
        ],
        [
            'nome' => 'Sumaia Costa',
            'email' => 'sumaia.costa@fundacaojari.org.br',
            'cod_tecnologia_lumis' => '8AE389DB307ADFD301308A4DFEA12DA8'
        ],
        [
            'nome' => 'Andréa Borges',
            'email' => 'andrea.borges@agua.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3023D13F013028540B51506F'
        ],
        [
            'nome' => 'João Conceição',
            'email' => 'joaoconceicao@unisinos.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D2478055264CB'
        ],
        [
            'nome' => 'Prof.a Brasilina Passarelli',
            'email' => 'r09ribeiro@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5F743A5B123B'
        ],
        [
            'nome' => 'JORGE LOPES RODRIGUES JÚNIOR',
            'email' => 'jorgeto_004@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB30099BB3013023050B2C042C'
        ],
        [
            'nome' => 'Dagmar Rivieri',
            'email' => 'diretoria@casadozezinho.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3144575A0131526E7C4A574D'
        ],
        [
            'nome' => 'Elizandra Martins Rocha de Paula',
            'email' => 'sipeb@sipeb.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3FF9514D0D89'
        ],
        [
            'nome' => 'Dimorvan Antônio Santos',
            'email' => 'dimorvansantos@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3E7FD149013E864BA8225431'
        ],
        [
            'nome' => 'Rachel Quandt Dias',
            'email' => 'rachel@incaper.es.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB4DE97DC9014DF81B373973BD'
        ],
        [
            'nome' => 'Andreia C. Gama',
            'email' => 'andreia.gama@vale.com',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DE8E970612700'
        ],
        [
            'nome' => 'ANDRÉIA TATIANE FALKOSKI',
            'email' => 'andreiaf_nh@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CC10786444C10'
        ],
        [
            'nome' => 'Beatriz Elaine Picini Magagna',
            'email' => 'beatriz.magagna@prof.uniso.br',
            'cod_tecnologia_lumis' => '8AE389DB321FF31F013225FCAD1A4B41'
        ],
        [
            'nome' => 'Sandra Edilene de Souza Barboza',
            'email' => 'ssouza@fundacaoromi.org.br',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E01309D58445B3286'
        ],
        [
            'nome' => 'Gislene Andrade',
            'email' => 'edisca@edisca.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31BF62120131BF76244B2611'
        ],
        [
            'nome' => 'Fabio de Lima Leite',
            'email' => 'fabioleite@ufscar.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5447125564B3'
        ],
        [
            'nome' => 'Ricardo Henrique Casini Chiarelli',
            'email' => 'ricardo.chiarelli@cati.sp.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB3EC3C5A1013EC6F4FC980F74'
        ],
        [
            'nome' => 'Isadora Wayhs Cadore Virgolin',
            'email' => 'mawayhs@unicruz.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D4476F0FB7F96'
        ],
        [
            'nome' => 'Enedina Teixeira',
            'email' => 'eteixeira@unicruz.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D4476F0FB7F96'
        ],
        [
            'nome' => 'Rozali Araújo',
            'email' => 'rozali@unicruz.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D4476F0FB7F96'
        ],
        [
            'nome' => 'Melissa Lenz',
            'email' => 'nutrilenz@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BC949DFA657E3'
        ],
        [
            'nome' => 'PAULO GRAEL',
            'email' => 'grael@zanizni.com.br',
            'cod_tecnologia_lumis' => '8AE389DB30413956013046EBF5F24813'
        ],
        [
            'nome' => 'Jacira Dias Ruiz',
            'email' => 'jacira@caritasrs.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3E7FD149013E89B27CA13155'
        ],
        [
            'nome' => 'Izabel Cristina Cruz de Lima',
            'email' => 'izabel@caritas.org.br',
            'cod_tecnologia_lumis' => '2C908A915B67132A015B7D652DC063CF'
        ],
        [
            'nome' => 'Rosa Maria Martins Pereira',
            'email' => 'rosinhammartins@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB31538968013154B4D26D2329'
        ],
        [
            'nome' => 'Patrícia Amorim Teixeira Loureiro',
            'email' => 'caritasceara@caritas.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3EED3B89013EF0804A600B7D'
        ],
        [
            'nome' => 'Pedro Urubatan Neto da Costa',
            'email' => 'urubatan@emater.tche.br',
            'cod_tecnologia_lumis' => '8AE389DB30B783670130B907B360673D'
        ],
        [
            'nome' => 'Dayse Valença',
            'email' => 'dayse@asplande.org.br',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B5D54ADC12C20'
        ],
        [
            'nome' => 'Samara Kelly Xavier e Silva',
            'email' => 'samaraxavier@fundacaocrianca.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30DCA5150130E5DB34F1726B'
        ],
        [
            'nome' => 'Maria Aparecida Silva Xavier',
            'email' => 'anasantosxavier@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F5CE8B0013F5D14025D6373'
        ],
        [
            'nome' => 'Maria Eneide Teixeira',
            'email' => 'eneideteixeira@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C45F7AA2B15D5'
        ],
        [
            'nome' => 'Vera Lúcia Anastácio',
            'email' => 'veranastacio@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C45F7AA2B15D5'
        ],
        [
            'nome' => 'Marcia Cristina José Rebelo',
            'email' => 'marciarebelo@30dejulho.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3F5DAD4A013F61F0ACF72751'
        ],
        [
            'nome' => 'Oscar Zalla Sampaio Neto',
            'email' => 'oscarsampaio@ufmt.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C318F01D81716'
        ],
        [
            'nome' => 'Cristina de Brito Ribeiro',
            'email' => 'cristina.ribeiro@abiorj.org',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C58FCD6C83951'
        ],
        [
            'nome' => 'Mariana Gama Semeghini',
            'email' => 'mari_anavilhanas@ipe.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F5DAD4A013F5E4391376447'
        ],
        [
            'nome' => 'Edmar José Scaloppi',
            'email' => 'edmar@fca.unesp.br',
            'cod_tecnologia_lumis' => '8AE389DB3E7FD149013E891DA25E4013'
        ],
        [
            'nome' => 'Soeni Domingos Sandreschi',
            'email' => 'adere@adere.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30D796850130DC5188906B73'
        ],
        [
            'nome' => 'RONALDO LOPES RODRIGUES MENDES',
            'email' => 'rlrmendes@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C49F7B86254A4'
        ],
        [
            'nome' => 'TANIA VITAL DA SILVA GOMES',
            'email' => 'tania.anjugomes@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB30FB9B8301310522061C7F49'
        ],
        [
            'nome' => 'Teresa Pazo',
            'email' => 'teresa@saudecrianca.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B01316C2619470817'
        ],
        [
            'nome' => 'Mara Helena Saalfeld',
            'email' => 'msaalfeld@emater.tche.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012FA263DE92787B'
        ],
        [
            'nome' => 'Marco Antonio dos Reis Pereira',
            'email' => 'pereira@feb.unesp.br',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DE412659C2383'
        ],
        [
            'nome' => 'Taise Andréa Triana',
            'email' => 'juridico@consegmaringa.org',
            'cod_tecnologia_lumis' => '8AE389DB30D147E40130D620B66E4834'
        ]
    ];

    protected $import_data_ultima_onda = [
        [
            'nome' => 'Silvia Regina Ramirez',
            'email' => 'silvia@projetopescar.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013438DDCFC51E03'
        ],
        [
            'nome' => 'José Afonso Bezerra Matias',
            'email' => 'afonsobezer@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013447B3E4EF39FF'
        ],
        [
            'nome' => 'Regina Helena Zacharias Martins',
            'email' => 'reginahzm@ig.com.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013447FED0B84606'
        ],
        [
            'nome' => 'Sandra Aparecida de Souza',
            'email' => 'sandraliana@ig.com.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013447FED0B84606'
        ],
        [
            'nome' => 'Josiane Masson',
            'email' => 'jmasson@artesol.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F097B9C012F12CF8EA42DC3'
        ],
        [
            'nome' => 'Luciano Cordoval de Barros',
            'email' => 'luciano.cordoval@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB2F36CF49012F4F6A2C2938FA'
        ],
        [
            'nome' => 'Paulo Eduardo de Aquino Ribeiro',
            'email' => 'paulo.eduardo@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB2F36CF49012F4F6A2C2938FA'
        ],
        [
            'nome' => 'Ederio Dino Bidoia',
            'email' => 'ederio.bidoia@unesp.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012F9C9D2B3B06E4'
        ],
        [
            'nome' => 'Ronaldo Carneiro de Sousa',
            'email' => 'ronaldocsousa@ig.com.br',
            'cod_tecnologia_lumis' => '8AE389DB321FF31F01326EB61CBD2B85'
        ],
        [
            'nome' => 'Silvianete Matos Carvalho',
            'email' => 'gentedefibra@assema.org.br',
            'cod_tecnologia_lumis' => '8AE389DB321FF31F01326EB61CBD2B85'
        ],
        [
            'nome' => 'Valdener Pereira Miranda',
            'email' => 'valdenerp@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB321FF31F01326EB61CBD2B85'
        ],
        [
            'nome' => 'Heraldo Firmino',
            'email' => 'heraldo@doutoresdaalegria.org.br',
            'cod_tecnologia_lumis' => '8AE389DB32BBF8A70132D623167E1EE1'
        ],
        [
            'nome' => 'Daiane Carina Paulo Ratão',
            'email' => 'daiane@doutoresdaalegria.org.br',
            'cod_tecnologia_lumis' => '8AE389DB32BBF8A70132D623167E1EE1'
        ],
        [
            'nome' => 'TAMMY ANGELINA CLARET',
            'email' => 'cpcjf@pjf.mg.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801343D68F56F7EA8'
        ],
        [
            'nome' => 'Nilo Barreto Falcão Filho',
            'email' => 'nilo.falcao@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB2F097B9C012F124F134919E7'
        ],
        [
            'nome' => 'Jorge Luiz Macau de Paiva',
            'email' => 'jorge.macau@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB2F097B9C012F124F134919E7'
        ],
        [
            'nome' => 'Anfrisio Pereira de Sousa',
            'email' => 'diretoria@cdi-df.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F1D7DDE012F1DADB84B1893'
        ],
        [
            'nome' => 'Eliane Luiz de Almeida',
            'email' => 'eliane@cpcd.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012F9CA71E621D1D'
        ],
        [
            'nome' => 'Silvia Valdez',
            'email' => 'administrativo@ibere.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30230F0E01302318B1BF1298'
        ],
        [
            'nome' => 'Geciane Jordani',
            'email' => 'tecnico@ibere.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30230F0E01302318B1BF1298'
        ],
        [
            'nome' => 'Andreza Portela Gomes',
            'email' => 'andreza@imagemagica.org',
            'cod_tecnologia_lumis' => '8AE389DB30DCA5150130E1B18120471C',
        ],
        [
            'nome' => 'Simone Rodrigues de Araújo',
            'email' => 'simone@imagemagica.org',
            'cod_tecnologia_lumis' => '8AE389DB30DCA5150130E1B18120471C'
        ],
        [
            'nome' => 'Eliane Luiz de Almeida',
            'email' => 'eliane@cpcd.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A80134240BFF5C3DC7'
        ],
        [
            'nome' => 'Aruan Braga',
            'email' => 'aruan@observatoriodefavelas.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801343DA6D0EC2638'
        ],
        [
            'nome' => 'Renata Teixeira Jardim',
            'email' => 'renata@themis.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801343DD3D4C87EDA'
        ],
        [
            'nome' => 'Luciano Cordoval de Barros',
            'email' => 'luciano.cordoval@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3484F6C10134A91582513798'
        ],
        [
            'nome' => 'Paulo Eduardo de Aquino Ribeiro',
            'email' => 'paulo.eduardo@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3484F6C10134A91582513798'
        ],
        [
            'nome' => 'Thadeu Rezende Provenza',
            'email' => 'thadeumamamiga@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB2F1D7DDE012F21F9E2C5797C'
        ],
        [
            'nome' => 'Newman Maria da Costa',
            'email' => 'newman.pereira@sebrae.com.br',
            'cod_tecnologia_lumis' => '8AE389DB2F1D7DDE012F3197654F579A'
        ],
        [
            'nome' => 'Fernando Rogério Costa Gomes',
            'email' => 'fernando.gomes@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB30099BB301300EB0090865D8'
        ],
        [
            'nome' => 'Raiana Araújo Ribeiro',
            'email' => 'raianaribeiro@aprendiz.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B01316C69A37B3C5C'
        ],
        [
            'nome' => 'Solange Ribeiro',
            'email' => 'solangeribeiro@aprendiz.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B01316C69A37B3C5C'
        ],
        [
            'nome' => 'Vera Maria Oliveira Carneiro',
            'email' => 'verinha01@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B01318B404316113E'
        ],
        [
            'nome' => 'Karla Renata Corrêa Viana',
            'email' => 'karla@childfundbrasil.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013438ABC7192EB0'
        ],
        [
            'nome' => 'Gabriel Barbosa',
            'email' => 'gabriel.barbosa@childfundbrasil.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013438ABC7192EB0'
        ],
        [
            'nome' => 'Peter Ribon Monteiro',
            'email' => 'peter.monteiro@fiamfaam.br',
            'cod_tecnologia_lumis' => '8AE389DB300404AE013004BCDCD23502'
        ],
        [
            'nome' => 'Braz Casagrande',
            'email' => 'braz.casagrande@fiamfaam.br',
            'cod_tecnologia_lumis' => '8AE389DB300404AE013004BCDCD23502'
        ],
        [
            'nome' => 'Sylvia Albernaz Machado do Carmo Guimarães',
            'email' => 'sylvia@vagalume.org.br',
            'cod_tecnologia_lumis' => '8AE389DB300404AE01300918CD6F6AE7'
        ],
        [
            'nome' => 'Rodrigo Zanella',
            'email' => 'rodrigo@vagalume.org.br',
            'cod_tecnologia_lumis' => '8AE389DB300404AE01300918CD6F6AE7'
        ],
        [
            'nome' => 'Lia Jamra Tsukomo',
            'email' => 'liajamra@vagalume.org.br',
            'cod_tecnologia_lumis' => '8AE389DB300404AE01300918CD6F6AE7'
        ],
        [
            'nome' => 'Eleni Crisóstomo de Oliveira Munguba',
            'email' => 'eleni.crisostomo@tjpe.jus.br',
            'cod_tecnologia_lumis' => '8AE389DB30230F0E0130236E325E2A0E'
        ],
        [
            'nome' => 'Élio Braz Mendes',
            'email' => 'elio.bm@tjpe.jus.br',
            'cod_tecnologia_lumis' => '8AE389DB30230F0E0130236E325E2A0E'
        ],
        [
            'nome' => 'Osmar Alves Lameira',
            'email' => 'osmar.lameira@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3023D13F0130287707634B3F'
        ],
        [
            'nome' => 'Rafael Kanke',
            'email' => 'rka@certi.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30D147E40130D29547886257'
        ],
        [
            'nome' => 'Selma Ramos Dau Bertagnoli',
            'email' => 'arrastao@arrastao.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B0131767B6B3372AA'
        ],
        [
            'nome' => 'Katya Delfino Silva',
            'email' => 'katya.delfino@arrastao.org.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B0131767B6B3372AA'
        ],
        [
            'nome' => 'Ivo Eduardo Roman Pons',
            'email' => 'ivopons@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B0131767B6B3372AA'
        ],
        [
            'nome' => 'Carlos Alberto Ferreira Mota',
            'email' => 'carlosmota-seas@santos.sp.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B013185922CF57E10'
        ],
        [
            'nome' => 'Magali Leite de Freitas',
            'email' => 'magalileite@santos.sp.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B013185922CF57E10'
        ],
        [
            'nome' => 'Rosana Maria Gomes',
            'email' => 'vovosabetudo@santos.sp.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B013185922CF57E10'
        ],
        [
            'nome' => 'Ana Christina Romano Mascarenhas',
            'email' => 'acmascarenhas@neoenergia.com',
            'cod_tecnologia_lumis' => '8AE389DB31667F8B013190880D224167'
        ],
        [
            'nome' => 'Pedro Boff',
            'email' => 'pboff@epagri.sc.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB31AA684D0131AA8B24EF4AF8'
        ],
        [
            'nome' => 'Marcelo Silva Pedroso',
            'email' => 'mpedroso@epagri.sc.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB31AA684D0131AA8B24EF4AF8'
        ],
        [
            'nome' => 'Paulo Antônio de Souza Gonçalves',
            'email' => 'pasg@epagri.sc.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB31AA684D0131AA8B24EF4AF8'
        ],
        [
            'nome' => 'Vilmar Francisco Zardo',
            'email' => 'zardo@epagri.sc.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB31AA684D0131AA8B24EF4AF8'
        ],
        [
            'nome' => 'Flávia Stela Gonçalves Vieira',
            'email' => 'redeterra@redeterra.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A801343E554B802B63'
        ],
        [
            'nome' => 'Gleydes Gambogi Parreira',
            'email' => 'ggambogi@icb.ufmg.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A80134429650DA7488'
        ],
        [
            'nome' => 'Mírian Nogueira Souza',
            'email' => 'mirian@caa.org.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013442CF70B434E8'
        ],
        [
            'nome' => 'Carlos Alberto Dayrell',
            'email' => 'carlosdayrell1@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013442CF70B434E8'
        ],
        [
            'nome' => 'Timóteo Leandro de Araujo',
            'email' => 'celafiscs.secretaria@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB33E135A8013442FFAF6F3D9C'
        ],
        [
            'nome' => 'Nelsa Inês Fabian Nespolo',
            'email' => 'nelsaifn@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB33E135A80134434732BF78B1'
        ],
        [
            'nome' => 'Aluísio Ribeiro Amaral Cavalcante',
            'email' => 'diretoria@casadaarvore.art.br',
            'cod_tecnologia_lumis' => '8AE389DB33E135A80134582FD7ED67FE'
        ],
        [
            'nome' => 'Ottorino Bonvini',
            'email' => 'rinobonvini@gmail.com',
            'cod_tecnologia_lumis' => '2C908A91622ED8C20162627A6E863FCF'
        ],
        [
            'nome' => 'Natália de Sousa Martins',
            'email' => 'natalia.msmcbj@gmail.com',
            'cod_tecnologia_lumis' => '2C908A91622ED8C20162627A6E863FCF'
        ],
        [
            'nome' => 'Ana Paula de Farias Fernandes',
            'email' => 'apaula.msmcbj@gmail.com',
            'cod_tecnologia_lumis' => '2C908A91622ED8C20162627A6E863FCF'
        ],
        [
            'nome' => 'Marcos José de Abreu',
            'email' => 'marcos@cepagro.org.br',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012F69BB9E2B4118'
        ],
        [
            'nome' => 'Sofia Silva Lemos',
            'email' => 'sofia.ecosan@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB2F55B253012F69BB9E2B4118'
        ],
        [
            'nome' => 'Rita de Cássia Castro Fernandes dos Santos ',
            'email' => 'projetocolorir@projetocolorir.org',
            'cod_tecnologia_lumis' => '8AE389DB2FBCD7A2012FC1E511F92F3C'
        ],
        [
            'nome' => 'Guilherme Henrique Pompiano do Carmo',
            'email' => 'guilhermepompiano@socioambiental.org',
            'cod_tecnologia_lumis' => '8AE389DB2FBCD7A2012FE0AE1EF002B7'
        ],
        [
            'nome' => 'Antônio Gomes Barbosa',
            'email' => 'barbosa@asabrasil.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3023D13F0130313A5A8C6D83'
        ],
        [
            'nome' => 'Maria Inês Andreotti Pereira Lara',
            'email' => 'ines@parceirosvoluntarios.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3041395601304773772C7C10'
        ],
        [
            'nome' => 'Guilherme Mielle Borba',
            'email' => 'guilherme@parceirosvoluntarios.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3041395601304773772C7C10'
        ],
        [
            'nome' => 'Priscila Ballestrin',
            'email' => 'priscila@parceirosvoluntarios.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3041395601304773772C7C10'
        ],
        [
            'nome' => 'Andrea Sperandio Ventura Braga',
            'email' => 'acavgente@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3065260B01306F8A321652D1'
        ],
        [
            'nome' => 'Onély Edwiges Teixeira',
            'email' => 'geplugar@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3065260B01306F8A321652D1'
        ],
        [
            'nome' => 'IDOLI CONTINI',
            'email' => 'arildocrespanmiguel@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB307016D2013071F9EDDA2227'
        ],
        [
            'nome' => 'Barbara Schmal',
            'email' => 'barbara@avive.org.br',
            'cod_tecnologia_lumis' => '8AE389DB307016D20130755510FE5CF2'
        ],
        [
            'nome' => 'ANDRE ANGELO THOMAZI',
            'email' => 'andrethomazi@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB307ADFD301307C4F331D1123'
        ],
        [
            'nome' => 'Marcia Benicio da Silva Cipriano',
            'email' => 'marciabenicioc@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E01309B4B9E9A42F7'
        ],
        [
            'nome' => 'Maria de Fatima Talarico Pestana',
            'email' => 'fatimatp@globo.com',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E01309B4B9E9A42F7'
        ],
        [
            'nome' => 'Sonia Benicio da Silva Jales',
            'email' => 'sonia.jales@cecapda.org.br',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E01309B4B9E9A42F7'
        ],
        [
            'nome' => 'Mariângela Terra Branco Camargos',
            'email' => 'mariangela@institutoagronelli.org.br',
            'cod_tecnologia_lumis' => '8AE389DB308A7E2E01309F18BD4136A3'
        ],
        [
            'nome' => 'Roselany de Oliveira Corrêa',
            'email' => 'roselany.correa@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB30D147E40130D166A8B54FA5'
        ],
        [
            'nome' => 'Dalva Maria Mota',
            'email' => 'dalva.mota@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB30D147E40130D166A8B54FA5'
        ],
        [
            'nome' => 'Gustavo Meyer',
            'email' => 'meyer_gustavo@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB30D147E40130D166A8B54FA5'
        ],
        [
            'nome' => 'Sylvia Albernaz Machado do Carmo Guimarães',
            'email' => 'sylvia@vagalume.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30FB9B8301310087AE2F7691'
        ],
        [
            'nome' => 'Lia Jamra Tsukomo',
            'email' => 'liajamra@vagalume.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30FB9B8301310087AE2F7691'
        ],
        [
            'nome' => 'Rodrigo Zanella',
            'email' => 'rodrigo@vagalume.org.br',
            'cod_tecnologia_lumis' => '8AE389DB30FB9B8301310087AE2F7691'
        ],
        [
            'nome' => 'Maria Clotilde Marcondes Carneiro',
            'email' => 'mariac.marcondes@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01311E5E002352D2'
        ],
        [
            'nome' => 'Arimar Feitosa Rodrigues',
            'email' => 'arimarcouros@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01311F44452C139B'
        ],
        [
            'nome' => 'Arlan Alves Pereira',
            'email' => 'alvez.27@outlook.com',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01311F44452C139B'
        ],
        [
            'nome' => 'Raimundo Jean Feitosa Rocha',
            'email' => 'jeanbelt_@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01311F44452C139B'
        ],
        [
            'nome' => 'Sergio Pimentel Vieira',
            'email' => 'sergiostm61@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA01311F44452C139B'
        ],
        [
            'nome' => 'Leocília Oliveira da Silva',
            'email' => 'leociliasilva@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA0131433DD6311107'
        ],
        [
            'nome' => 'Maria das Graças Santiago de Moura Rosa',
            'email' => 'copescarte@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3119C8CA0131433DD6311107'
        ],
        [
            'nome' => 'Francisca Schaich Prates',
            'email' => 'contato@institutotroca.org',
            'cod_tecnologia_lumis' => '8AE389DB3144575A01314F6F5D8F2EFA'
        ],
        [
            'nome' => 'Lya Lena Garcia de Lacerda',
            'email' => 'projetoecobolsa@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3144575A01314F6F5D8F2EFA'
        ],
        [
            'nome' => 'Ângela Maria Schossler',
            'email' => 'meioambiente@estrela-rs.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3144575A013151A17B16796F'
        ],
        [
            'nome' => 'Inês Candiotto Grassi',
            'email' => 'amcajuruena@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3144575A013152D9F5981E49'
        ],
        [
            'nome' => 'Tiliâno Martin de Siqueira',
            'email' => 'martinarquiteto68@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3144575A013152F992103CE5'
        ],
        [
            'nome' => 'Izabel Cristina da Silva Santos',
            'email' => 'izabelcss16@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3144575A0131532864C24038'
        ],
        [
            'nome' => 'Luciano Monteiro da Silva',
            'email' => 'lucianmmonteiro@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3144575A013153470BFC6953'
        ],
        [
            'nome' => 'Marilene Maia',
            'email' => 'marilene@unisinos.br',
            'cod_tecnologia_lumis' => '8AE389DB315389680131538F85C84C5E'
        ],
        [
            'nome' => 'Claudenir Carolino Barbosa',
            'email' => 'nossacooperarte@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB315389680131538E384438A0'
        ],
        [
            'nome' => 'Selma Ramos Dau Bertagnoli',
            'email' => 'arrastao@arrastao.org.br',
            'cod_tecnologia_lumis' => '8AE389DB327A237E013282F08359724E'
        ],
        [
            'nome' => 'Katya Delfino Silva',
            'email' => 'katya.delfino@arrastao.org.br',
            'cod_tecnologia_lumis' => '8AE389DB327A237E013282F08359724E'
        ],
        [
            'nome' => 'Vagner Lemes da Silva',
            'email' => 'social@arrastao.org.br',
            'cod_tecnologia_lumis' => '8AE389DB327A237E013282F08359724E'
        ],
        [
            'nome' => 'Monique Figueiredo Barboza',
            'email' => 'adm.centralveredas@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB327A237E0132871E6DAA4C3A'
        ],
        [
            'nome' => 'Ronildo Monteiro Ferreira',
            'email' => 'ronildo.monteiro@united-purpose.org',
            'cod_tecnologia_lumis' => '8AE389DB32BBF8A70132CF1FEB475817'
        ],
        [
            'nome' => 'Eliane Luiz de Almeida',
            'email' => 'eliane@cpcd.org.br',
            'cod_tecnologia_lumis' => '8AE389DB32BBF8A70132D035B4DA584D'
        ],
        [
            'nome' => 'Aurigele Alves Barbosa',
            'email' => 'aurigele@adel.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3D837807013DA19670D5760F'
        ],
        [
            'nome' => 'RODRIGO RIBEIRO FRANCO VIEIRA',
            'email' => 'rodrigo.franco@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3DF408C6013E32DD93DC0C61'
        ],
        [
            'nome' => 'JOSE LITO MENEZES DE SOUZA',
            'email' => 'joselito.menezes@codevasf.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB3DF408C6013E32DD93DC0C61'
        ],
        [
            'nome' => 'FREDERICO ORLANDO CALAZANS MACHADO',
            'email' => 'frederico.calazans@codevasf.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB3DF408C6013E32DD93DC0C61'
        ],
        [
            'nome' => 'Paulo César Nunes',
            'email' => 'paulojuruena@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3DF408C6013E33B43787089A'
        ],
        [
            'nome' => 'Lucinéia Machado da Silva',
            'email' => 'lucineia4@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3DF408C6013E33B43787089A'
        ],
        [
            'nome' => 'Raiana Araújo Ribeiro',
            'email' => 'raianaribeiro@aprendiz.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3E38D10D013E5739D4296D20'
        ],
        [
            'nome' => 'Solange Ribeiro',
            'email' => 'solangeribeiro@aprendiz.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3E38D10D013E5739D4296D20'
        ],
        [
            'nome' => 'Maria Dalmira de Camargo Andrade',
            'email' => 'arq.camargoandrade@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3E6C051A013E70028ECD1D91'
        ],
        [
            'nome' => 'LEILA MATTOS',
            'email' => 'mattos.leila@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3E7FD149013E816A9CC06940'
        ],
        [
            'nome' => 'Mariana Estevão de Souza Moraes',
            'email' => 'mestevao1@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3E7FD149013E865134077635'
        ],
        [
            'nome' => 'Osvaldo Ryohei Kato',
            'email' => 'osvaldo.kato@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3E94513A013E9F65FDBB5521'
        ],
        [
            'nome' => 'Anna Christina Monteiro Roffé Borges',
            'email' => 'anna.roffe@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3E94513A013E9F65FDBB5521'
        ],
        [
            'nome' => 'Celia Maria Braga Calandrini de Azevedo',
            'email' => 'celia.azevedo@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3E94513A013E9F65FDBB5521'
        ],
        [
            'nome' => 'Debora Veiga Aragão',
            'email' => 'debora.aragao@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3E94513A013E9F65FDBB5521'
        ],
        [
            'nome' => 'Lucilda Maria Sousa de Matos',
            'email' => 'lucilda.matos@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3E94513A013E9F65FDBB5521'
        ],
        [
            'nome' => 'Mauricio Kadooka Shimizu',
            'email' => 'mauricio.shimizu@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3E94513A013E9F65FDBB5521'
        ],
        [
            'nome' => 'Steel Silva Vasconcelos',
            'email' => 'steel.vasconcelos@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3E94513A013E9F65FDBB5521'
        ],
        [
            'nome' => 'Tatiana Deane de Abreu Sá',
            'email' => 'tatiana.sa@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3E94513A013E9F65FDBB5521'
        ],
        [
            'nome' => 'Alfredo Kingo Oyama Homma',
            'email' => 'alfredo.homma@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3EB2F1D3013EC1DBF39532C6'
            ],
        [
            'nome' => 'José Edmar Urano de Carvalho',
            'email' => 'jose.urano-carvalho@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3EB2F1D3013EC1DBF39532C6'
        ],
        [
            'nome' => 'Antônio José Elias Amorim de Menezes',
            'email' => 'antonio.menezes@embrapa.br',
            'cod_tecnologia_lumis' => '8AE389DB3EB2F1D3013EC1DBF39532C6'
        ],
        [
            'nome' => 'Juliana Simionato Costa',
            'email' => 'juliana.simionato@teto.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3EB2F1D3013EC35EC72F5E1F'
        ],
        [
            'nome' => 'Rodrigo Vieira dos Santos',
            'email' => 'rodrigo.vieira@teto.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3EB2F1D3013EC35EC72F5E1F'
        ],
        [
            'nome' => 'Deusimar Cândido de Oliveira',
            'email' => 'deusimarfrutosdosertao@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EDA4903013EDC6AA41129D5'
        ],
        [
            'nome' => 'Aécio Santiago',
            'email' => 'aeciofsantiago@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EDA4903013EDC6AA41129D5'
        ],
        [
            'nome' => 'José Lima Castro Júnior',
            'email' => 'castro.junior@sda.ce.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB3EDA4903013EE14CCBA7443A'
        ],
        [
            'nome' => 'Viviany Maria Mota Macedo',
            'email' => 'viviany.mota@sda.ce.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB3EDA4903013EE14CCBA7443A'
        ],
        [
            'nome' => 'Monica Rabelo de Freitas Moreira',
            'email' => 'diretoria@portaliep.com',
            'cod_tecnologia_lumis' => '8AE389DB3EE680E6013EE7096FDC01E9'
        ],
        [
            'nome' => 'Gilson Antunes da Silva',
            'email' => 'gilson.silva@fiocruz.br',
            'cod_tecnologia_lumis' => '8AE389DB3EE7F3E9013EE8818C2218C2'
        ],
        [
            'nome' => 'Flávia Passos Soares',
            'email' => 'flaviapsoares1@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EE7F3E9013EE8818C2218C2'
        ],
        [
            'nome' => 'Natasha Mendes Gabriel',
            'email' => 'natasha@institutoelos.org',
            'cod_tecnologia_lumis' => '8AE389DB3EE7F3E9013EEC76C2573B9C'
        ],
        [
            'nome' => 'Val Rocha',
            'email' => 'val@institutoelos.org',
            'cod_tecnologia_lumis' => '8AE389DB3EE7F3E9013EEC76C2573B9C'
        ],
        [
            'nome' => 'Normandes Matos da Silva',
            'email' => 'normandes@ufmt.br',
            'cod_tecnologia_lumis' => '8AE389DB3EE7F3E9013EECC86BC641EE'
        ],
        [
            'nome' => 'Carlos Henrique Bonsi Checoli',
            'email' => 'ameo.brasil1@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EE7F3E9013EECC86BC641EE'
        ],
        [
            'nome' => 'William Pietro de Souza',
            'email' => 'william_pietro@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EE7F3E9013EECC86BC641EE'
        ],
        [
            'nome' => 'Sílvia Regina de Toledo Cabra',
            'email' => 'silviareginacabral@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3EED3B89013EF18DA7003058'
        ],
        [
            'nome' => 'Edinalva Pinheiro dos Santos Oliveira',
            'email' => 'edinalvapinheiro63@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EED3B89013EF1ABC92E32F6'
        ],
        [
            'nome' => 'Wanessa Jully Pinheiro Oliveira',
            'email' => 'wanessapinheiro82@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EED3B89013EF1ABC92E32F6'
        ],
        [
            'nome' => 'Maria Miranda de Moraes',
            'email' => 'moraes.miranda@ig.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3EED3B89013EF1ABC92E32F6'
        ],
        [
            'nome' => 'NEILA BARBOSA OSORIO',
            'email' => 'neilaosorio@uft.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB3EED3B89013EFAEE51FD7548'
        ],
        [
            'nome' => 'LUIZ SINÉSIO SILVA NETO',
            'email' => 'luizsinesio@uft.edu.br',
            'cod_tecnologia_lumis' => '8AE389DB3EED3B89013EFAEE51FD7548'
        ],
        [
            'nome' => 'Hernani Alves da Silva',
            'email' => 'hernanialves@emater.pr.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB3EFC56E7013EFD59395F7895'
        ],
        [
            'nome' => 'Paulo Tadatoshi Hiroki',
            'email' => 'paulohiroki@emater.pr.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB3EFC56E7013EFD59395F7895'
        ],
        [
            'nome' => 'Francisco Samonek',
            'email' => 'franciscosamonek@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3EFC56E7013F079875E5375E'
        ],
        [
            'nome' => 'Bernardina de Sena',
            'email' => 'seninhadesena@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EFC56E7013F0B9B2EEA55CD'
        ],
        [
            'nome' => 'Patrícia Mara Lacerda Santos',
            'email' => 'tissabh@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EFC56E7013F0B9B2EEA55CD'
        ],
        [
            'nome' => 'Valdete da Silva Cordeiro',
            'email' => 'meninasdesinha@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3EFC56E7013F0B9B2EEA55CD'
        ],
        [
            'nome' => 'Aluísio Ribeiro Amaral Cavalcante',
            'email' => 'diretoria@casadaarvore.art.br',
            'cod_tecnologia_lumis' => '8AE389DB3F10C3B0013F23F83C7C2C52'
        ],
        [
            'nome' => 'Alda Júlia Calheiros Amorim Santos',
            'email' => 'incubadorapindorama123@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F3DEE69BF012B'
        ],
        [
            'nome' => 'Maria Solange Rodrigues Fialho',
            'email' => 'solangefialho1@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F3DEE69BF012B'
        ],
        [
            'nome' => 'Janieide Pereira Lima',
            'email' => 'janieidelima@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F3DEE69BF012B'
        ],
        [
            'nome' => 'Renata Maria Cavalcanti Pessoa',
            'email' => 'renata_cavalcanti2@wvi.org',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F443B10957700'
        ],
        [
            'nome' => 'Renato Brufatti',
            'email' => 'renatoecdo@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB3F388B79013F49BF1C10755A'
        ],
        [
            'nome' => 'Raquel Motta do Amaral ',
            'email' => 'raquel.amaral@institutomusiva.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F541F5B013F58DBDAFF1C91'
        ],
        [
            'nome' => 'Valmir do Vale Lins',
            'email' => 'valmir.vale@institutomusiva.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F541F5B013F58DBDAFF1C91'
        ],
        [
            'nome' => 'Elaine Cristina Ricci',
            'email' => 'naniricci@homail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F541F5B013F5CA5A6854368'
        ],
        [
            'nome' => 'Camila Thomé',
            'email' => 'cahthome08@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F541F5B013F5CA5A6854368'
        ],
        [
            'nome' => 'Ubirajara Carvalho',
            'email' => 'birafoto@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F66AA74013F671EBD8B7088'
        ],
        [
            'nome' => 'Francisco Valdean',
            'email' => 'valdeancotidiano@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F66AA74013F671EBD8B7088'
        ],
        [
            'nome' => 'André Luiz Siqueira',
            'email' => 'andre@riosvivos.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F66AA74013F67799ADB60D4'
        ],
        [
            'nome' => 'Vanessa Spacki',
            'email' => 'vanessa.spacki@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB3F66AA74013F67799ADB60D4'
        ],
        [
            'nome' => 'Alcides Bartolomeu de Faria',
            'email' => 'ecoa@riosvivos.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F66AA74013F67799ADB60D4'
        ],
        [
            'nome' => 'Danilo Silva Miranda',
            'email' => 'infobarueri@projov.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F67D393013F67F93B5C1ECF'
        ],
        [
            'nome' => 'Sonia Maria Sabbag',
            'email' => 'sonia.sabbag@projov.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F67D393013F67F93B5C1ECF'
        ],
        [
            'nome' => 'Solange Aparecida da Silva',
            'email' => 'solange@arrastao.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F6823FE013F68A14E4A193F'
        ],
        [
            'nome' => 'Selma Ramos Dau Bertagnoli',
            'email' => 'arrastao@arrastao.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F6823FE013F68A14E4A193F'
        ],
        [
            'nome' => 'Katya Delfino Silva',
            'email' => 'katya.delfino@arrastao.org.br',
            'cod_tecnologia_lumis' => '8AE389DB3F6823FE013F68A14E4A193F'
        ],
        [
            'nome' => 'Jacira Teresinha Dias Ruiz',
            'email' => 'jacira@caritasrs.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014C94153AC51D10'
        ],
        [
            'nome' => 'Teresinha Steffens',
            'email' => 'biblioteca_maripa@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014C94803DEA45F4'
        ],
        [
            'nome' => 'Aluísio Ribeiro Amaral Cavalcante',
            'email' => 'diretoria@casadaarvore.art.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CAA8FF2771B33'
        ],
        [
            'nome' => 'Érica Sacchi Zanotti',
            'email' => 'erica@consuladodamulher.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CB96911636B17'
        ],
        [
            'nome' => 'Katia Barreto Lima',
            'email' => 'katia@consuladodamulher.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CB96911636B17'
        ],
        [
            'nome' => 'Alessandro Santos de Carvalho',
            'email' => 'alessandro@consuladodamulher.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CB96911636B17'
        ],
        [
            'nome' => 'Aurigele Alves Barbosa',
            'email' => 'aurigele@adel.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4C7AF3F5014CC85131633092'
        ],
        [
            'nome' => 'Anderson Benites Carneiro',
            'email' => 'benites.anderson@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014CFC126E933E09'
        ],
        [
            'nome' => 'Denise Silva ',
            'email' => 'denisemiranda83@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014CFC126E933E09'
        ],
        [
            'nome' => 'MATHEUS MICHELAN BATISTA',
            'email' => 'matheusmichelan@umuarama.pr.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D0652A8C61F82'
        ],
        [
            'nome' => 'FERNANDA PERIARD MANTOVANI',
            'email' => 'meioambiente@umuarama.pr.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D0652A8C61F82'
        ],
        [
            'nome' => 'Solange Bottaro',
            'email' => 'solange@ramacrisna.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D0A81123858E4'
        ],
        [
            'nome' => 'Cledemar Duarte',
            'email' => 'coordantenados@ramacrisna.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D0A81123858E4'
        ],
        [
            'nome' => 'Ricardo Affonso Ferreira',
            'email' => 'ricardo@eds.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D208AED247E73'
        ],
        [
            'nome' => 'Marcia Abdala',
            'email' => 'marcia@eds.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4CE6B108014D208AED247E73'
        ],
        [
            'nome' => 'Maria Auxiliadora Drumond',
            'email' => 'dodoradrumondbh@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D430DB6CA492F'
        ],
        [
            'nome' => 'André Souza Noronha Nepomuceno',
            'email' => 'azn@certi.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D4E64930F2616'
        ],
        [
            'nome' => 'Marcos Da-Ré',
            'email' => 'mda@certi.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D4E64930F2616'
        ],
        [
            'nome' => 'Rafael Kamke',
            'email' => 'rka@certi.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D4E64930F2616'
        ],
        [
            'nome' => 'Maria José Barbosa de Souza Aquino',
            'email' => 'zitabsouza1@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D58CC3A9C0E69'
        ],
        [
            'nome' => 'Raquel Ribeiro ',
            'email' => 'eduqativo@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D590D22D0714E'
        ],
        [
            'nome' => 'Adalto Gomes',
            'email' => 'comitetijucas@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D684D29C56519'
        ],
        [
            'nome' => 'Fabrício Noveletto',
            'email' => 'fabricio.noveletto@udesc.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D687ED44E7B1B'
        ],
        [
            'nome' => 'Antonia Nágela de Araújo Costa',
            'email' => 'nagela.costa@inec.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D6C03D4B71C0F'
        ],
        [
            'nome' => 'Carlos Reni Araújo Dino',
            'email' => 'reni.dino@inec.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D6C03D4B71C0F'
        ],
        [
            'nome' => 'Juliana Matos Ferreira',
            'email' => 'juliana.ferreira@inec.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D6C03D4B71C0F'
        ],
        [
            'nome' => 'Juliana Simionato Costa',
            'email' => 'juliana.simionato@teto.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D7672DDFF16CA'
        ],
        [
            'nome' => 'Rodrigo Vieira dos Santos',
            'email' => 'rodrigo.vieira@teto.org.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D7672DDFF16CA'
        ],
        [
            'nome' => 'SANDRA APARECIDA ALVES',
            'email' => 'sanddrap@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8C2E5AB20D47'
        ],
        [
            'nome' => 'Daniel Luis Albuquerque da Silva',
            'email' => 'danielluis.pe@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8C4C0F0C5316'
        ],
        [
            'nome' => 'Maria Elizabeth',
            'email' => 'bethdeoxum@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8C4C0F0C5316'
        ],
        [
            'nome' => 'Adna Priscila de Andrade Silva',
            'email' => 'priscilaadna@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8C4C0F0C5316'
        ],
        [
            'nome' => 'Eva Vilaseca Corominas',
            'email' => 'eva.vilaseca.c@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8DA73BEB3A16'
        ],
        [
            'nome' => 'Gabriel Neira Voto',
            'email' => 'gabrielvoto@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8DA73BEB3A16'
        ],
        [
            'nome' => 'Henrique dos Santos Nascimento',
            'email' => 'henriquesn.rj@gmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8DA73BEB3A16'
        ],
        [
            'nome' => 'Miguel Plaza de Blas',
            'email' => 'ges2@hotmail.com',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D8DA73BEB3A16'
        ],
        [
            'nome' => 'Rudimar Élvio da Silva',
            'email' => 'desenvolvimentorural@farroupilha.rs.gov.br',
            'cod_tecnologia_lumis' => '8AE389DB4D2AD70E014D966AA0541811'
        ],
        [
            'nome' => 'Dinaldo Antonio dos Santos',
            'email' => 'dinaldotec1@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014DB954B18D0396'
        ],
        [
            'nome' => 'Ernesto Katsunori Suzuki',
            'email' => 'ekatsu1@yahoo.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4D971C95014DB954B18D0396'
        ],
        [
            'nome' => 'Maria Sueli Fonseca Gonçalves ',
            'email' => 'suelizinha@uol.com.br',
            'cod_tecnologia_lumis' => '8AE389DB4DC44513014DE88084BC0998'
        ],
        [
            'nome' => 'Gonçalo Guimarães',
            'email' => 'goncalo@itcp.coppe.ufrj.br',
            'cod_tecnologia_lumis' => '8AE389DB4DE97DC9014DF838ED7B5E53'
        ],
        [
            'nome' => 'Eliane Ribeiro Pereira',
            'email' => 'eliane@itcp.coppe.ufrj.br',
            'cod_tecnologia_lumis' => '8AE389DB4DE97DC9014DF838ED7B5E53'
        ],
        [
            'nome' => 'ALEX DECIAN THOMAZI',
            'email' => 'alex.thomazi@aldeiasinfantis.org.br',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B590E739B5A45'
        ],
        [
            'nome' => 'Tamires Löw Gonçalves',
            'email' => 'tamires.low@aldeiasinfantis.org.br',
            'cod_tecnologia_lumis' => '2C908A915B021ED7015B590E739B5A45'
        ],
        [
            'nome' => 'Rodrigo Nunes Souto',
            'email' => 'rodrigo@colivre.coop.br',
            'cod_tecnologia_lumis' => '2C908A915B67132A015B67E9F49B45E8'
        ],
        [
            'nome' => 'Lilian do Prado Silva',
            'email' => 'lilianprado.acreditar@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915B67132A015B8763F21027AF'
        ],
        [
            'nome' => 'ERIVALDO DE SOUZA PAIVA',
            'email' => 'erivaldopaivaadm@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BA252278C1CDC'
        ],
        [
            'nome' => 'Ricardo Luiz Rocha Ramalho Cavalcanti',
            'email' => 'itviva@uol.com.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BCA7BFF803F65'
        ],
        [
            'nome' => 'Leandro Lima Casado dos Santos',
            'email' => 'leolima_adv@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BCA7BFF803F65'
        ],
        [
            'nome' => 'Amanda dos Santos Sousa Camilo',
            'email' => 'assousa@alphaville.com.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BCF92216723B3'
        ],
        [
            'nome' => 'Debora Silva e Silva',
            'email' => 'dssilva@alphaville.com.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BCF92216723B3'
        ],
        [
            'nome' => 'Fernanda Toledo Oliveira',
            'email' => 'ftoledo@alphaville.com.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BCF92216723B3'
        ],
        [
            'nome' => 'Maria das Graças de Oliveira',
            'email' => 'graca@alphaville.com.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BCF92216723B3'
        ],
        [
            'nome' => 'Monique Boufleur Long',
            'email' => 'monique.long@ipti.org.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BD490E84A0B11'
        ],
        [
            'nome' => 'Lenoir Santos',
            'email' => 'lenoirsantos@uol.com.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BD9CAC43C567B'
        ],
        [
            'nome' => 'Raimunda Menezes dos Santos',
            'email' => 'ictamtecnologia@bol.com.br',
            'cod_tecnologia_lumis' => '2C908A915B8D2458015BD9CAC43C567B'
        ],
        [
            'nome' => 'Fernando José Lobo',
            'email' => 'zelobo@ta.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015BEEE6BE626422'
        ],
        [
            'nome' => 'João Lacerda',
            'email' => 'jglacerda@ta.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015BEEE6BE626422'
        ],
        [
            'nome' => 'André Luiz Siqueira',
            'email' => 'andre@riosvivos.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015BF90181FD0C01'
        ],
        [
            'nome' => 'Juliano Thomé',
            'email' => 'juliano.thome@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015BF90181FD0C01'
        ],
        [
            'nome' => 'Vanessa Spacki',
            'email' => 'vanessa@riosvivos.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015BF90181FD0C01'
        ],
        [
            'nome' => 'Selma Pacheco Barata Ramos',
            'email' => 'selmapacheco@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C05834C62371E'
        ],
        [
            'nome' => 'Madeline Abreu Monteiro',
            'email' => 'edisca@edisca.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C1177CABC338D'
        ],
        [
            'nome' => 'Mônica Sillan de Oliveira',
            'email' => 'facc.4042@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C176AB2902EBC'
        ],
        [
            'nome' => 'Alex Bager',
            'email' => 'abager@ecoestradas.org',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C1D0B6F3E1C75'
        ],
        [
            'nome' => 'Helena Gomes Bonumá',
            'email' => 'helenabonuma2014@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C1D20E207379A'
        ],
        [
            'nome' => 'Maria do Carmo Duarte de Bittencourt',
            'email' => 'mariabitt@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C1D20E207379A'
        ],
        [
            'nome' => 'Eliane Vieira da Rocha',
            'email' => 'admpajeu@casadamulherdonordeste.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C30A70C9B0C01'
        ],
        [
            'nome' => 'Diego Tessitore Schultz ',
            'email' => 'diego.schultz@akatu.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3B4BA8F54A53'
        ],
        [
            'nome' => 'Denise Silva',
            'email' => 'desi.apae@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3B67C5962705'
        ],
        [
            'nome' => 'Juliana Nicole Nahring',
            'email' => 'ju.apae@terra.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3B67C5962705'
        ],
        [
            'nome' => 'Maikon Dias',
            'email' => 'projetos.maikondias@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3B67C5962705'
        ],
        [
            'nome' => 'Luis Tadeu Assad',
            'email' => 'assadmar@iabs.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3CAC67EF3E2E'
        ],
        [
            'nome' => 'Marcela Pimenta Campos Coutinho',
            'email' => 'marcela@iabs.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3CAC67EF3E2E'
        ],
        [
            'nome' => 'Ricardo Nonô',
            'email' => 'ricardo.nono@iabs.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C3CAC67EF3E2E'
        ],
        [
            'nome' => 'Teofro Lacerda Gomes',
            'email' => 'teofro2009@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C428F62C821C4'
        ],
        [
            'nome' => 'Adriano Belisario Feitosa da Costa',
            'email' => 'adrianobf@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C46BDB9FD135C'
        ],
        [
            'nome' => 'Bruno Freitas',
            'email' => 'bfreitas@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C46BDB9FD135C'
        ],
        [
            'nome' => 'Bruno Vianna',
            'email' => 'bruno@pobox.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C46BDB9FD135C'
        ],
        [
            'nome' => 'Marcelo Saldanha',
            'email' => 'instituto@bemestarbrasil.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C46BDB9FD135C'
        ],
        [
            'nome' => 'Rodrigo Bortolini Troian',
            'email' => 'rtroian@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C46BDB9FD135C'
        ],
        [
            'nome' => 'Ana Ines Tacite',
            'email' => 'atacite@coca-cola.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C46C4897E20D6'
        ],
        [
            'nome' => 'Isa Cristina da Rocha Lopes',
            'email' => 'islopes@coca-cola.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C46C4897E20D6'
        ],
        [
            'nome' => 'Selma Maria Costa Paula',
            'email' => 'projetocbnossa93@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C46F5AEC84EBC'
        ],
        [
            'nome' => 'Jorge Luís de Paula',
            'email' => 'jluisdepaula20@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C46F5AEC84EBC'
        ],
        [
            'nome' => 'Daniel Yacoub Belllissimo',
            'email' => 'daniel@institutoterroa.org',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C49AD36277736'
        ],
        [
            'nome' => 'Luís Fernando Iozzi Beitum',
            'email' => 'fernando@institutoterroa.org',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C49AD36277736'
        ],
        [
            'nome' => 'Gabriel Bianconi Fernandes',
            'email' => 'gabriel@aspta.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C4A9ED070022F'
        ],
        [
            'nome' => 'Luciano Marçal da Silveira',
            'email' => 'luciano@aspta.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C4A9ED070022F'
        ],
        [
            'nome' => 'Paulo Frederico Petersen',
            'email' => 'paulo@aspta.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C4A9ED070022F'
        ],
        [
            'nome' => 'SANDRA CRISTINA DIAS DO VALE',
            'email' => 'vale.s@promundo.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C4F73E0A92C93'
        ],
        [
            'nome' => 'Débora Cristina da Silva',
            'email' => 'cmtsinos@unisinos.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C547E5485187B'
        ],
        [
            'nome' => 'Jeferson Müller Timm',
            'email' => 'jeferson@ambientaldaterra.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C547E5485187B'
        ],
        [
            'nome' => 'Marcia Chame dos Santos',
            'email' => 'marcia.chame@fiocruz.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C548CF3696D89'
        ],
        [
            'nome' => 'José Antonio Gonçalves Leme',
            'email' => 'jleme@sp.gov.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C54E040555441'
        ],
        [
            'nome' => 'Luciana Alves Fontes',
            'email' => 'caritasteresina@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5564C57717DA'
        ],
        [
            'nome' => 'Paulo Henrique Squinzani',
            'email' => 'paulomeioambiente.sti@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C560D673D2953'
        ],
        [
            'nome' => 'Cíntia Miguel Kaefer',
            'email' => 'cintiamkaefer@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C599BB4AF08E8'
        ],
        [
            'nome' => 'Claudia Cabeda',
            'email' => 'claudiacabeda@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C599BB4AF08E8'
        ],
        [
            'nome' => 'Rosélia Araújo Vianna',
            'email' => 'roselia@genesesocial.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C599BB4AF08E8'
        ],
        [
            'nome' => 'Marcos Guilherme Belchior de Araújo',
            'email' => 'gbelchior@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C59AA686A731B'
        ],
        [
            'nome' => 'Isabelle Macedo Gomes',
            'email' => 'isabellegomes@roquettepinto.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C59E3448F501E'
        ],
        [
            'nome' => 'Clarinha Glock',
            'email' => 'clarinhaglock@uol.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5A97790D0602'
        ],
        [
            'nome' => 'Juliano Machado do Nascimento',
            'email' => 'juliano.nascimento@maristas.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5A97790D0602'
        ],
        [
            'nome' => 'Nederson Menezes Cardoso',
            'email' => 'nederson.cardoso@maristas.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5A97790D0602'
        ],
        [
            'nome' => 'Arlete Vicente Nunes',
            'email' => 'arlete_vicente@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5BD829A7526E'
        ],
        [
            'nome' => 'Macicleia Ressurreição de Freitas',
            'email' => 'sinha_cleo@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5BD829A7526E'
        ],
        [
            'nome' => 'Moane Vieira Sousa',
            'email' => 'moanes_mo@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5BD829A7526E'
        ],
        [
            'nome' => 'Ana Maria Viana Pinto',
            'email' => 'anapintoj26@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5DFB63276FCB'
        ],
        [
            'nome' => 'VILMAR SIMION NASCIMENTO',
            'email' => 'vilmar@programandoofuturo.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5E48F9612B50'
        ],
        [
            'nome' => 'Andréa de Barros Barreto',
            'email' => 'dedeiabarreto@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5E9CD5133AF7'
        ],
        [
            'nome' => 'Vinicius do Nascimento',
            'email' => 'i.kairos@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5E9CD5133AF7'
        ],
        [
            'nome' => 'Gabriela Pieroni',
            'email' => 'guabijuba@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5F1E644256FD'
        ],
        [
            'nome' => 'Georges Schnyder',
            'email' => 'g.schnyder@slowfoodbrasil.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5F1E644256FD'
        ],
        [
            'nome' => 'Marina Vianna Ferreira',
            'email' => 'marina.vf@uol.com.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C5F1E644256FD'
        ],
        [
            'nome' => 'Kamila de Oliveira Rocha',
            'email' => 'kamila@mbc.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C6057B8966A1F'
        ],
        [
            'nome' => 'Paulo José de Santana',
            'email' => 'paulosantana@serta.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C60BD6E4B0DD1'
        ],
        [
            'nome' => 'Abdalaziz de Moura Xavier de Moraes',
            'email' => 'moura@serta.org.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C60BD6E4B0DD1'
        ],
        [
            'nome' => 'Rafaele Nunes Jacques',
            'email' => 'rnjacques@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C60BD6E4B0DD1'
        ],
        [
            'nome' => 'Laís Naoko Higashi',
            'email' => 'laishigashi@litrodeluz.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C60EF07844BD9'
        ],
        [
            'nome' => 'Rodrigo Eidy Uemura',
            'email' => 'rodrigo.eidy.uemura@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C60EF07844BD9'
        ],
        [
            'nome' => 'Moisés de Souza Modesto Júnior',
            'email' => 'moises.modesto@embrapa.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C64B5AB1144A1'
        ],
        [
            'nome' => 'Raimundo Nonato Brabo Alves',
            'email' => 'raimundo.brabo-alves@embrapa.br',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C64B5AB1144A1'
        ],
        [
            'nome' => 'Antonio Uilian Rebouças Fiuza',
            'email' => 'uiliian_aw@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C6A55BB22173B'
        ],
        [
            'nome' => 'Florisvaldo Pereira Mascarenhas Junior',
            'email' => 'jragroecologo@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C6A55BB22173B'
        ],
        [
            'nome' => 'Joelton Belau da Silva',
            'email' => 'joeltonbelau@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915BEDB4C3015C6A55BB22173B'
        ],
        [
            'nome' => 'Marina dos Reis Massagardi',
            'email' => 'marinadrm@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C7EA9D1AF1A82'
        ],
        [
            'nome' => 'Eduardo Bello Rodrigues',
            'email' => 'edubello1@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C83B9863F2824'
        ],
        [
            'nome' => 'Raiana Araújo Ribeiro',
            'email' => 'raianaribeiro@aprendiz.org.br',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C89B410CC3093'
        ],
        [
            'nome' => 'Solange Ribeiro',
            'email' => 'solangeribeiro@aprendiz.org.br',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C89B410CC3093'
        ],
        [
            'nome' => 'Ana Carolina Steinkopf',
            'email' => 'carol.steinkopf@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C9CA330645671'
        ],
        [
            'nome' => 'Priscila Batista da Silva',
            'email' => 'institutoverdevida@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015C9DB1D1A31F3E'
        ],
        [
            'nome' => 'Marcio Domingos Carvalhal de Moura',
            'email' => 'carvalhalmarcio@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015CA6F8D06A578F'
        ],
        [
            'nome' => 'Nádia Helena Oliveira Almeida',
            'email' => 'nadia.maranguape@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915C7E0AA7015CA6F8D06A578F'
        ],
        [
            'nome' => 'Francisco de Paula Antunes Lima',
            'email' => 'frapalima@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CADE6D6103620'
        ],
        [
            'nome' => 'William Azalim do Valle',
            'email' => 'williamazalim@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CADE6D6103620'
        ],
        [
            'nome' => 'Ana Glécia da Silva Almeida',
            'email' => 'anaglecia@moc.org.br',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CB1C9DCB8527C'
        ],
        [
            'nome' => 'Dafne Herrero',
            'email' => 'dafneherrero@drabrincadeira.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CB820671A5270'
        ],
        [
            'nome' => 'Daniela Signorini Marcilio',
            'email' => 'daniela.signorini.marcilio@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CB820671A5270'
        ],
        [
            'nome' => 'Bianca Soares Ramos',
            'email' => 'biancaramos@movimentodown.org.br',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CB90E0872123B'
        ],
        [
            'nome' => 'Clayton de Souza Nobre',
            'email' => 'clayton.nobre@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CBD8FFB470E1D'
        ],
        [
            'nome' => 'Irlana Toledo Cassini',
            'email' => 'irlanacassini@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CBD8FFB470E1D'
        ],
        [
            'nome' => 'Jasmine Fajardo Giovannini',
            'email' => 'jasmine@foradoeixo.org.br',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CBD8FFB470E1D'
        ],
        [
            'nome' => 'DIONETE FIGUEREDO BARBOZA',
            'email' => 'copabase@gmail.com',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CC0D5D0196829'
        ],
        [
            'nome' => 'Carlos Augusto Rodrigues de Sena',
            'email' => 'diretoriageral@ongmandacaru.org.br',
            'cod_tecnologia_lumis' => '2C908A915CA752CC015CC12C6DC60676'
        ],
        [
            'nome' => 'Amanda Silveira Carbone',
            'email' => 'amanda_scarbone@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01693A7861A147AD'
        ],
        [
            'nome' => 'Samia Nascimento Sulaiman',
            'email' => 'samia.sulaiman@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01693A7861A147AD'
        ],
        [
            'nome' => 'Sonia Maria Viggiani Coutinho',
            'email' => 'scoutinho@usp.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01693A7861A147AD'
        ],
        [
            'nome' => 'Maria Bernadete Gonçalves',
            'email' => 'bel.dourada@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01695DBAC27A01DE'
        ],
        [
            'nome' => 'ALEXANDRE MATSCHINSKE',
            'email' => 'alexandremats@imap.curitiba.pr.gov.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E016971F971EE0B22'
        ],
        [
            'nome' => 'GABRIELA FRANCO BERGER APPEL',
            'email' => 'gappel@imap.curitiba.pr.gov.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E016971F971EE0B22'
        ],
        [
            'nome' => 'GUILHERME SELL',
            'email' => 'gsell@imap.curitiba.pr.gov.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E016971F971EE0B22'
        ],
        [
            'nome' => 'Patricia Cota Gomes',
            'email' => 'patricia@imaflora.org',
            'cod_tecnologia_lumis' => '2C908A9169264A1E016976D3015F13FA'
        ],
        [
            'nome' => 'Fabio Antonio Muller Mariano',
            'email' => 'fabiomuller@cieds.org.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01697D2197242009'
        ],
        [
            'nome' => 'Fernanda Colmenero Melo de Moura',
            'email' => 'fcolmenero.rj@cieds.org.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01697D2197242009'
        ],
        [
            'nome' => 'Jose Claudio da Costa Barros',
            'email' => 'joseclaudio.rj@cieds.org.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01697D2197242009'
        ],
        [
            'nome' => 'Fabio Antonio Muller Mariano',
            'email' => 'fabiomuller@cieds.org.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01697D39150629E9'
        ],
        [
            'nome' => 'Fabio Antonio Muller Mariano',
            'email' => 'fabiomuller@cieds.org.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01697D39F6622BA7'
        ],
        [
            'nome' => 'Valrei Lima',
            'email' => 'valrei.rj@cieds.org.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01697D39F6622BA7'
        ],
        [
            'nome' => 'Karen Worcman',
            'email' => 'karen@museudapessoa.net',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169826566171711'
        ],
        [
            'nome' => 'Lucas Ferreira de Lara',
            'email' => 'lucas@museudapessoa.net',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169826566171711'
        ],
        [
            'nome' => 'Sônia London',
            'email' => 'sonia@museudapessoa.net',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169826566171711'
        ],
        [
            'nome' => 'Elissa Fichtler',
            'email' => 'elissa@pimpmycarroca.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E016982FFF3AF7CFA'
        ],
        [
            'nome' => 'MARCIO SILVA ANDRADE',
            'email' => 'marcioandrade@iftm.edu.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01698605C7AF7819'
        ],
        [
            'nome' => 'Pedro Henrique Tomás',
            'email' => 'pedrohenrique@iftm.edu.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01698605C7AF7819'
        ],
        [
            'nome' => 'Ana Carla Albuquerque da Cunha Marinho',
            'email' => 'empreendeler@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E01699B7F82A4533E'
        ],
        [
            'nome' => 'Ana Carolina Souza da Silva',
            'email' => 'ana.ssouza15@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169A69CA3C64C97'
        ],
        [
            'nome' => 'Diogo Majerowicz Maneschy',
            'email' => 'diogommaneschy@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169A69CA3C64C97'
        ],
        [
            'nome' => 'Juliana Sarcinelli Menezes',
            'email' => 'julianasarcinelli93@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169A69CA3C64C97'
        ],
        [
            'nome' => 'Paolo de Castro Martins Massoni',
            'email' => 'massoni.paolo@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169A69CA3C64C97'
        ],
        [
            'nome' => 'Roberta Donati Pignatari Vilela Guerra',
            'email' => 'robertadpvg@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169A69CA3C64C97'
        ],
        [
            'nome' => 'Álvaro Luz Alves Coutinho',
            'email' => 'alvaro.coutinho@usp.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169A6A727CD5FD0'
        ],
        [
            'nome' => 'Fernanda Giannini Veirano',
            'email' => 'institutoterramater@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169A6A727CD5FD0'
        ],
        [
            'nome' => 'Julia Madeira',
            'email' => 'juliamadeirag@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169A6A727CD5FD0'
        ],
        [
            'nome' => 'Jony William Villela Vianna',
            'email' => 'jonywilliamv@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169A7E466A50F08'
        ],
        [
            'nome' => 'Aluísio Ribeiro Amaral Cavalcante',
            'email' => 'diretoria@casadaarvore.art.br',
            'cod_tecnologia_lumis' => '2C908A9169264A1E0169B4E1BC96532B'
        ],
        [
            'nome' => 'Francinete Rodrigues Lima',
            'email' => 'francinete.lima@fas-amazonas.org',
            'cod_tecnologia_lumis' => '2C908A9169BAC5C20169BF4BF1F7196A'
        ],
        [
            'nome' => 'Gislaine Cruz',
            'email' => 'gislaine.cruz@fas-amazonas.org',
            'cod_tecnologia_lumis' => '2C908A9169BAC5C20169BF4BF1F7196A'
        ],
        [
            'nome' => 'Gláucio Dias Gonçalves',
            'email' => 'glaucio@cfaf.org.br',
            'cod_tecnologia_lumis' => '2C908A9169BAC5C20169C968C4B04432'
        ],
        [
            'nome' => 'Marcos Alan Magalhães Novais',
            'email' => 'marcos@cfaf.org.br',
            'cod_tecnologia_lumis' => '2C908A9169BAC5C20169C968C4B04432'
        ],
        [
            'nome' => 'Rita Maria de Cássia Bittencourt Cardoso',
            'email' => 'ritacardoso@cfaf.org.br',
            'cod_tecnologia_lumis' => '2C908A9169BAC5C20169C968C4B04432'
        ],
        [
            'nome' => 'Emanuel Heliomar Medeiros de Souza',
            'email' => 'emanuel.medeiros.souza@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169BAC5C20169C9AB06DE03B2'
        ],
        [
            'nome' => 'Francisco de Oliveira Nascimento',
            'email' => 'fonoolliver@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A9169BAC5C20169C9AB06DE03B2'
        ],
        [
            'nome' => 'Ana Gabriela Simões Borges',
            'email' => 'anab@grpcom.com.br',
            'cod_tecnologia_lumis' => '2C908A9169BAC5C20169C9E8D48F42F7'
        ],
        [
            'nome' => 'Luciane Casorillo Travain',
            'email' => 'ltravain@grpcom.com.br',
            'cod_tecnologia_lumis' => '2C908A9169BAC5C20169C9E8D48F42F7'
        ],
        [
            'nome' => 'Crislene Rodrigues da Silva Morais',
            'email' => 'crislenemorais@yahoo.com.br ',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169CC1EF19B3FE4'
        ],
        [
            'nome' => 'Eduardo dos Santos Pacifico',
            'email' => 'eduardo.pacifico@gaiamais.org',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169DA4A0ABB13EC'
        ],
        [
            'nome' => 'Camila Cheibub Figueiredo',
            'email' => 'c.camila.figueiredo@educardpaschoal.org.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169DE351F8E1CEE'
        ],
        [
            'nome' => 'Cristiane Annunciato Stefanelli',
            'email' => 'cristiane.stefanelli@educardpaschoal.org.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169DE351F8E1CEE'
        ],
        [
            'nome' => 'Juliana Aparecida da Silva',
            'email' => 'julianasilva@litrodeluz.com',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169E5888E4E1C3B'
        ],
        [
            'nome' => 'Lais Naoko Higashi',
            'email' => 'laishigashi@litrodeluz.com',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169E5888E4E1C3B'
        ],
        [
            'nome' => 'Tainara Pereira Caldas',
            'email' => 'tainaracaldas@litrodeluz.com',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169E5888E4E1C3B'
        ],
        [
            'nome' => 'Josiane da Silva Sousa Mattos',
            'email' => 'sousamattos28@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169E86DEC220FE9'
        ],
        [
            'nome' => 'Guilherme Valladares',
            'email' => 'guilherme@perene.org.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169E89F5D3C2A82'
        ],
        [
            'nome' => 'Giovani Novelli Pereira',
            'email' => 'giovaninovelli@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169E9165E992A3E'
        ],
        [
            'nome' => 'Kawoana Trautman Vianna',
            'email' => 'kawoana@cientistabeta.com.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169E9165E992A3E'
        ],
        [
            'nome' => 'Antonio Dumont Machado do Nascimento',
            'email' => 'dumonzinho@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169E955DCF774CB'
        ],
        [
            'nome' => 'Cleidson Carpeggiane Santos Araujo',
            'email' => 'cleidsoncarpeggiane@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169E955DCF774CB'
        ],
        [
            'nome' => 'Marcelo Francia Arco-Verde',
            'email' => 'marcelo.arco-verde@embrapa.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169EE8A5C930579'
        ],
        [
            'nome' => 'Flávio Rodrigues da Silva',
            'email' => 'rodrigues.manifesto@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169EE9EBC533438'
        ],
        [
            'nome' => 'Raquel Regina Duarte Moreira',
            'email' => 'raquel.moreira@unesp.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169EE9EBC533438'
        ],
        [
            'nome' => 'Sergio Azevedo Fonseca',
            'email' => 'sergio.fonseca@unesp.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169EE9EBC533438'
        ],
        [
            'nome' => 'Alvir Longhi',
            'email' => 'alvir@cetap.org.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169EEDF0BC235C4'
        ],
        [
            'nome' => 'João Paulo Machado Godoy',
            'email' => 'pedagogico@sete.org.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169F328FA1D4557'
        ],
        [
            'nome' => 'Pedro Meloni Nassar',
            'email' => 'pedro.nassar@mamiraua.org.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169FD18BB802B49'
        ],
        [
            'nome' => 'Clarice Linhares',
            'email' => 'superintendencia@providencia.org.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169FD428ADD79C8'
        ],
        [
            'nome' => 'Terezinha Nascimento',
            'email' => 'assessoria@providencia.org.br',
            'cod_tecnologia_lumis' => '2C908A9169CA01780169FD428ADD79C8'
        ],
        [
            'nome' => 'Heliane Gomes de Azevedo',
            'email' => 'relacionamento@institutoippe.com.br',
            'cod_tecnologia_lumis' => '2C908A9169FEABF50169FEB97B7D0DB7'
        ],
        [
            'nome' => 'Maira Gabriel Anhorn',
            'email' => 'marialeixo@redesdamare.org.br',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A03D3B31466CF'
        ],
        [
            'nome' => 'Francisca Alcivania de Melo Silva',
            'email' => 'alcivania.silva@unesp.br',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A070FD9287EBB'
        ],
        [
            'nome' => 'Lucas Florencio Mariano',
            'email' => 'lucas@registro.unesp.br',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A070FD9287EBB'
        ],
        [
            'nome' => 'Luis Carlos Ferreira de Almeida',
            'email' => 'luiscarlos@registro.unesp.br',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A070FD9287EBB'
        ],
        [
            'nome' => 'Marcelo Vieira Ferraz',
            'email' => 'ferraz@registro.unesp.br',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A070FD9287EBB'
        ],
        [
            'nome' => 'Ocimar José Baptista Bim',
            'email' => 'ocimarbim@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A070FD9287EBB'
        ],
        [
            'nome' => 'Reginaldo Barboza da Silva',
            'email' => 'regbarboza@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A070FD9287EBB'
        ],
        [
            'nome' => 'Rogerio Haruo Sakai',
            'email' => 'rogerio.sakai@cati.sp.gov.vr',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A070FD9287EBB'
        ],
        [
            'nome' => 'Vania Gnaspini',
            'email' => 'coordenacao@andrefrancovive.org.br',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A07AABE251504'
        ],
        [
            'nome' => 'Adriana Lobo Jucá',
            'email' => 'adrianajuca@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A08FCF9AB1136'
        ],
        [
            'nome' => 'Daniela Tavares Gontijo',
            'email' => 'danielatgontijo@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A08FCF9AB1136'
        ],
        [
            'nome' => 'Sémares Genuíno Vieira',
            'email' => 'semaresvieira@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A08FCF9AB1136'
        ],
        [
            'nome' => 'Danilo Henrique Araujo Ladentim',
            'email' => 'daniloladentim.cadifrg@gmail.com',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A0C1B99E22059'
        ],
        [
            'nome' => 'Mauricio José Silva Cunha',
            'email' => 'mauricio.cunha@cadi.org.br',
            'cod_tecnologia_lumis' => '2C908A9169FEABF5016A0C1B99E22059'
        ],
        [
            'nome' => 'Mauricio Aurélio dos Santos',
            'email' => 'presidente@casalarluzdocaminho.org',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0D76FBC52228'
        ],
        [
            'nome' => 'Luiz Pasquali ',
            'email' => 'luizpasquali@emater.pr.gov.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0D956C1177F7'
        ],
        [
            'nome' => 'Charem Jordânia G. Cruz',
            'email' => 'sharem.j16@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0DDC5EAF3814'
        ],
        [
            'nome' => 'Claudia Marco',
            'email' => 'claudia.marco@ufca.edu.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0DDC5EAF3814'
        ],
        [
            'nome' => 'Fagner Soares Farias',
            'email' => 'fagnerfarias@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0DDC5EAF3814'
        ],
        [
            'nome' => 'Gerlandio Ramalho Silva',
            'email' => 'gerlandio.ramalho2011@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0DDC5EAF3814'
        ],
        [
            'nome' => 'José Lucas da Silva Neto',
            'email' => 'lucasneto1274@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0DDC5EAF3814'
        ],
        [
            'nome' => 'Rawell Souza Costa',
            'email' => 'pedroaguiarneto@terra.com.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0DDC5EAF3814'
        ],
        [
            'nome' => 'Rawell Souza Costa',
            'email' => 'rawell01@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0DDC5EAF3814'
        ],
        [
            'nome' => 'Vinicius Ferreira Lobo',
            'email' => 'vinicius_f_lobo@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0DDC5EAF3814'
        ],
        [
            'nome' => 'Ana Dillon Nunes',
            'email' => 'img.mov@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A0DFDAD4623FE'
        ],
        [
            'nome' => 'Ednalva Aparecida de Moura dos Santos',
            'email' => 'ednalva@sermais.org.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A1193716A7DD6'
        ],
        [
            'nome' => 'Wandreza Aparecida Ferreira Bayona',
            'email' => 'wandreza@sermais.org.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A1193716A7DD6'
        ],
        [
            'nome' => 'IZABEL CRISTINA DA SILVA SANTOS',
            'email' => 'izabelcss16@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A12A1C0355563'
        ],
        [
            'nome' => 'JOSELHA DE FARIAS VICENTE',
            'email' => 'avedaterra@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A12A1C0355563'
        ],
        [
            'nome' => 'ANA MARIA SALES PLACIDINO',
            'email' => 'REDEKODYABAHIA@GMAIL.COM',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A132DA42B1844'
        ],
        [
            'nome' => 'Raimundo Nonato Pereira da Silva',
            'email' => 'acbantunacional@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A132DA42B1844'
        ],
        [
            'nome' => 'Aruan Francisco Diogo Braga',
            'email' => 'aruan@observatoriodefavelas.org.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A177B442E5E75'
        ],
        [
            'nome' => 'Jorge Luiz Barbosa',
            'email' => 'jorge@observatoriodefavelas.org.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A177B442E5E75'
        ],
        [
            'nome' => 'Lino Teixeira',
            'email' => 'linotex7@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A177B442E5E75'
        ],
        [
            'nome' => 'Alice Lima de Menezes Vasconcelos',
            'email' => 'gia.ong@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A1CFC673478CC'
        ],
        [
            'nome' => 'Mario Henriques Saladini',
            'email' => 'msaladini@sesc.com.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A214837610AB0'
        ],
        [
            'nome' => 'Marcelo Leal Teles da Silva',
            'email' => 'marcelolealts@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A216080D97481'
        ],
        [
            'nome' => 'Guido Lemos Souza Filho',
            'email' => 'guido@lavid.ufpb.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A21C51E5C58CB'
        ],
        [
            'nome' => 'Manuella Aschoff Lima',
            'email' => 'manuella.lima@lavid.ufpb.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A21C51E5C58CB'
        ],
        [
            'nome' => 'Rostand Edson Oliveira Costa',
            'email' => 'rostand@lavid.ufpb.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A21C51E5C58CB'
        ],
        [
            'nome' => 'Tiago Maritan Ugulino de Araújo',
            'email' => 'tiagomaritan@lavid.ufpb.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A21C51E5C58CB'
        ],
        [
            'nome' => 'Graciete Gonçalves dos Santos',
            'email' => 'graciete@casadamulherdonordeste.org.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A2279B8994948'
        ],
        [
            'nome' => 'Itanacy Ramos de Oliveira',
            'email' => 'itanacy@casadamulherdonordeste.org.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A2279B8994948'
        ],
        [
            'nome' => 'Édina Accorsi',
            'email' => 'smecepedagogico@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A228AB89E7FBA'
        ],
        [
            'nome' => 'Ruanceli do Nascimento Santos',
            'email' => 'ruan@ipti.org.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A22DBB59D5D72'
        ],
        [
            'nome' => 'Saulo Faria Almeida Barretto',
            'email' => 'saulo@ipti.org.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A22DBB59D5D72'
        ],
        [
            'nome' => 'MARIA FLÁVIA VANUCCI DE MORAES',
            'email' => 'mariaflaviavanucci@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A22DCF01160B1'
        ],
        [
            'nome' => 'Antonio George Salgado Helt',
            'email' => 'icbe@icbe.org.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A25E208812606'
        ],
        [
            'nome' => 'Ana Paula de Carvalho Sudbrack',
            'email' => 'apsudbrack@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A265E7FF438C3'
        ],
        [
            'nome' => 'Bruno Chagas Alves Fernandes',
            'email' => 'bruno.fernandes@osorio.ifrs.edu.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A265E7FF438C3'
        ],
        [
            'nome' => 'Diana Cabral Cavalcanti',
            'email' => 'diana.cavalcanti@osorio.ifrs.edu.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A265E7FF438C3'
        ],
        [
            'nome' => 'Gislaine Teixeira Ferreira',
            'email' => 'gtfnutri@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A265E7FF438C3'
        ],
        [
            'nome' => 'José Paulo Oliveira Filho',
            'email' => 'oliveira.filho237@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A265E7FF438C3'
        ],
        [
            'nome' => 'Lucas Jardim da Silva',
            'email' => 'lcsjardim@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A265E7FF438C3'
        ],
        [
            'nome' => 'Gisele Mariuse da Silva',
            'email' => 'primeirainfanciamelhor@saude.rs.gov.br',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A269A599008BE'
        ],
        [
            'nome' => 'Gleyse Maria do Couto Peiter',
            'email' => 'gleysep@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A26E39BB30A37'
        ],
        [
            'nome' => 'Marcos Roberto Carmona',
            'email' => 'marcoscoepbrasil@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A0D622D016A26E39BB30A37'
        ],
        [
            'nome' => 'Carina Guedes de Mendonça',
            'email' => 'ca.guedes@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A277686E36E8D'
        ],
        [
            'nome' => 'Rafaela Dias Lopes',
            'email' => 'perifeitura@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A277686E36E8D'
        ],
        [
            'nome' => 'Afonso Rabelo',
            'email' => 'rabeloafonso@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2820EAA656BA'
        ],
        [
            'nome' => 'Felipe França Moraes',
            'email' => 'franca@inpa.gov.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2820EAA656BA'
        ],
        [
            'nome' => 'Gláucio Belém da Silva',
            'email' => 'glaucyo40@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2820EAA656BA'
        ],
        [
            'nome' => 'Luis Eduardo Cardoso de Almeida Salvatore',
            'email' => 'luis@brasilsolidario.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A28AB05A65EE4'
        ],
        [
            'nome' => 'José Rogaciano Siqueira de Oliveira',
            'email' => 'rogacianoo@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2924D084121F'
        ],
        [
            'nome' => 'Fernando Pires Aristimunho',
            'email' => 'fernando@fld.com.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2B630CB96682'
        ],
        [
            'nome' => 'Juliana Mazurana',
            'email' => 'juliana@fld.com.br ',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2B630CB96682'
        ],
        [
            'nome' => 'Junia Maria Paiva',
            'email' => 'dilson@humanabrasil.org',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2B778D3E5DE2'
        ],
        [
            'nome' => 'Fernando Pires Aristimunho',
            'email' => 'fernando@fld.com.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2C2E8F3A060B'
        ],
        [
            'nome' => 'Juliana Mazurana',
            'email' => 'juliana@fld.com.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2C2E8F3A060B'
        ],
        [
            'nome' => 'Julia Rovena Witt',
            'email' => 'julia@fld.com.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2C2E8F3A060B'
        ],
        [
            'nome' => 'Carlos Augusto Antunes',
            'email' => 'carlosaugustoantunes@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2C2653ED5EBC'
        ],
        [
            'nome' => 'Suzete Souza Oselame',
            'email' => 'suzioselame@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2C2653ED5EBC'
        ],
        [
            'nome' => 'Jesiel Pereira de Campos Silva',
            'email' => 'jesiel77@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2C979FC34FD6'
        ],
        [
            'nome' => 'Waldineia Ribeiro de Almeida ',
            'email' => 'waldineiaalmeida@cultura.mt.gov.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2CA263364CD7'
        ],
        [
            'nome' => 'Marina Vieira Souza',
            'email' => 'marina@iniciativaverde.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2CA5D1BD6EC9'
        ],
        [
            'nome' => 'Roberto Ulisses Resende',
            'email' => 'roberto@iniciativaverde.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2CA5D1BD6EC9'
        ],
        [
            'nome' => 'CRISTIANLEX SOARES DOS SANTOS',
            'email' => 'crispj2010@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2D281CE92F1C'
        ],
        [
            'nome' => 'Thiago Alves da Silva Costa',
            'email' => 'thiago.s.costa@pbh.gov.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2D689EE54941'
        ],
        [
            'nome' => 'Ronaldo Munenori Endo',
            'email' => 'ronaldo.kami@institutoseb.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A2DC592EA7A88'
        ],
        [
            'nome' => 'Mauren Porciúncula Moreira da Silva',
            'email' => 'mauren@furg.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A307A4B35446A'
        ],
        [
            'nome' => 'Roberaldo Carvalho de Souza',
            'email' => 'rcsouza@ctec.ufal.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A309587D851FC'
        ],
        [
            'nome' => "Cristiane Lopes Carneiro D'Albuquerque",
            'email' => 'clcsouza.pi@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A30B5CC706B36'
        ],
        [
            'nome' => 'Lila Cristina Xavier Luz',
            'email' => 'lilaxavier@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A30B5CC706B36'
        ],
        [
            'nome' => 'Marlúcia Valéria da Silva',
            'email' => 'valeriasilvathe@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A30B5CC706B36'
        ],
        [
            'nome' => 'Lissa Kawai',
            'email' => 'lissa@institutouno.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A30DE0DF23A46'
        ],
        [
            'nome' => 'Rubens Salles de Carvalho Junior',
            'email' => 'rubens@institutouno.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A30DE0DF23A46'
        ],
        [
            'nome' => 'Thaís Jorge',
            'email' => 'thais.jorge@institutouno.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A30DE0DF23A46'
        ],
        [
            'nome' => 'Gioia Matilde Alba Tumbiolo Tosi',
            'email' => 'tumbiologioia@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A32045BC90692'
        ],
        [
            'nome' => 'Gisele da Silva Craveiro',
            'email' => 'giselesc@usp.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A32045BC90692'
        ],
        [
            'nome' => 'Letticia de Paula Diez Rey',
            'email' => 'saopaulo.coord@osbrasil.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A32045BC90692'
        ],
        [
            'nome' => 'Nelsa Inês Fabian Nespolo',
            'email' => 'nelsaifn@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A35B8268745F5'
        ],
        [
            'nome' => 'Jayse Antonio da Silva Ferreira',
            'email' => 'jayseantonio@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A3835A5115AC9'
        ],
        [
            'nome' => 'Paulo Rodrigues Agra',
            'email' => 'coopaal.coop@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A3B20D7C74C55'
        ],
        [
            'nome' => 'Aécio Flávio Santiago Araújo',
            'email' => 'aeciofsantiago@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A40BAB24B7B41'
        ],
        [
            'nome' => 'Deusimar Candido de Oliveira ',
            'email' => 'deusimarfrutosdosertao@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A40BAB24B7B41'
        ],
        [
            'nome' => 'Isabelle Aparecida Costa',
            'email' => 'isabellecosta.iac@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A3FF6AFF6471C'
        ],
        [
            'nome' => 'Kelen Menezes Flores Rossi de Aguiar',
            'email' => 'kelenaguiar@utfpr.edu.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A3FF6AFF6471C'
        ],
        [
            'nome' => 'Rafael Admar Bini',
            'email' => 'rafaelbini@utfpr.edu.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A3FF6AFF6471C'
        ],
        [
            'nome' => 'Ricardo Schneider',
            'email' => 'rschneider@utfpr.edu.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A3FF6AFF6471C'
        ],
        [
            'nome' => 'José Ranieri Santos Ferreira',
            'email' => 'falecomranieriferreira@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A4593EF685F89'
        ],
        [
            'nome' => 'Luciana Gazoto Migotti',
            'email' => 'falecom@aril.com.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A55AF65042041'
        ],
        [
            'nome' => 'Paula Lazzarini',
            'email' => 'psicologia@aril.com.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A55AF65042041'
        ],
        [
            'nome' => 'Alex Priver Decian Thomazi',
            'email' => 'alex.thomazi@aldeiasinfantis.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A563CE6BE68BA'
        ],
        [
            'nome' => 'Bruno Vinícius Nascimento de Oliveira',
            'email' => 'bruno.oliveira@aldeiasinfantis.org.br ',
            'cod_tecnologia_lumis' => '2C908A916A274124016A563CE6BE68BA'
        ],
        [
            'nome' => 'Ana Luíza Alvarenga Barbosa',
            'email' => 'ninha.conectacultura@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A68E5C3BC0DB1'
        ],
        [
            'nome' => 'Dagmar Teixeira Bedê',
            'email' => 'dagbede@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A68E5C3BC0DB1'
        ],
        [
            'nome' => 'Deise Alves Eleutério',
            'email' => 'deise.eleuterio@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A68E5C3BC0DB1'
        ],
        [
            'nome' => 'Elisângela Maria de Jesus',
            'email' => 'elismariadejesus@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A68E5C3BC0DB1'
        ],
        [
            'nome' => 'Joviano Gabriel Maia Mayer',
            'email' => 'mayerjoviano@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A68E5C3BC0DB1'
        ],
        [
            'nome' => 'Luciana Lanza de Melo Franco',
            'email' => 'luza.lanza@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A68E5C3BC0DB1'
        ],
        [
            'nome' => 'GABRIEL LIMA LAMEIRINHAS',
            'email' => 'gabriel@sth.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A68FDAE13225F'
        ],
        [
            'nome' => 'IGOR VINICIUS ALVARENGA MARINELLI',
            'email' => 'igor@sth.org.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A68FDAE13225F'
        ],
        [
            'nome' => 'Quesia do Rosario Reis',
            'email' => 'casadorio@casadorio.org',
            'cod_tecnologia_lumis' => '2C908A916A274124016A69497BD66377'
        ],
        [
            'nome' => 'Thiago Cavalli Azambuja',
            'email' => 't_cavalli@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A916A274124016A69497BD66377'
        ],
        [
            'nome' => 'Daniela Pantuso',
            'email' => 'dpantuso@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A695E5FF709B5'
        ],
        [
            'nome' => 'Marina Utsch',
            'email' => 'guiadepermacultura@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A274124016A695E5FF709B5'
        ],
        [
            'nome' => 'Luciana Chinaglia Quintão',
            'email' => 'info@bancodealimentos.org.br',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A6F4F94C40A5A'
        ],
        [
            'nome' => 'Natalia Rodrigues',
            'email' => 'natalia@bancodealimentos.org.br',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A6F4F94C40A5A'
        ],
        [
            'nome' => 'Eduardo Moutinho Ramalho Bittencourt',
            'email' => 'eduardomrbittencourt@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A7DD4AC260C0C'
        ],
        [
            'nome' => 'Felipe Nogueira Bello Simas',
            'email' => 'fnbsimas@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A8DB324467984'
        ],
        [
            'nome' => 'Fernanda Maria Coutinho de Andrade',
            'email' => 'fernandamcandrade@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A8DB324467984'
        ],
        [
            'nome' => 'Márcio Gomes da Silva',
            'email' => 'marcio.gomes@ufv.br',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A8DB324467984'
        ],
        [
            'nome' => 'Tatiana Pires Barrella',
            'email' => 'tatiana.barrella@ufv.br',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A8DB324467984'
        ],
        [
            'nome' => 'Tommy Flávio Cardoso Wanick Loureiro de Sousa',
            'email' => 'tommywanick@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A8DB324467984'
        ],
        [
            'nome' => 'Cláudia Bandeira',
            'email' => 'claudia.bandeira@acaoeducativa.org.br',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A8E8460132B93'
        ],
        [
            'nome' => 'Edneia Gonçalves',
            'email' => 'edneia.goncalves@acaoeducativa.org.br ',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A8E8460132B93'
        ],
        [
            'nome' => 'Carmen Tereza Salvini',
            'email' => 'cataventoprojetando@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A9208D7244932'
        ],
        [
            'nome' => 'Rosalina Nogueira da Silva',
            'email' => 'ferdbd@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A9208D7244932'
        ],
        [
            'nome' => 'Abdalaziz de Moura Xavier de Moraes',
            'email' => 'moura@serta.org.br',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A9256B16A3EE4'
        ],
        [
            'nome' => 'Paulo José de Santana',
            'email' => 'paulosantana@serta.org.br',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A9256B16A3EE4'
        ],
        [
            'nome' => 'Valdiane Soares da Silva',
            'email' => 'valdiane@serta.org.br',
            'cod_tecnologia_lumis' => '2C908A916A6E42A1016A9256B16A3EE4'
        ],
        [
            'nome' => 'Gillys Vieira da Silva',
            'email' => 'gvsilva@solmarista.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016A932A02B47D6F'
        ],
        [
            'nome' => 'Sabrina Maria da Silva',
            'email' => 'sbsilva@solmarista.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016A932A02B47D6F'
        ],
        [
            'nome' => 'Stefany Carolina Ramos Cruz',
            'email' => 'stefany.ramos@solmarista.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016A932A02B47D6F'
        ],
        [
            'nome' => 'Suzi Mari Calixto',
            'email' => 'scalixto@solmarista.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016A932A02B47D6F'
        ],
        [
            'nome' => 'Aquiles Vasconcelos Simões',
            'email' => 'aqsimoes@pq.cnpq.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016A9785F82B4CE9'
        ],
        [
            'nome' => 'Eliana Teles Rodrigues',
            'email' => 'elianteles@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016A9785F82B4CE9'
        ],
        [
            'nome' => 'Paulo Fernando da Silva Martins',
            'email' => 'pfsm@ufpa.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016A9785F82B4CE9'
        ],
        [
            'nome' => 'Lorena Lucas Sasaki',
            'email' => 'lorenals13@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A927996016A97E9DECC0F34'
        ],
        [
            'nome' => 'Solange Sato Simões',
            'email' => 'satosolange@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A927996016A97E9DECC0F34'
        ],
        [
            'nome' => 'Valeria Sucena Hammes',
            'email' => 'vshammes@uol.com.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016A97E9DECC0F34'
        ],
        [
            'nome' => 'DAIANE NEPEL MARINS',
            'email' => 'dany.marins@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A927996016A9CC9D9021B96'
        ],
        [
            'nome' => 'Julia Letícia Helmer Brum',
            'email' => 'efabe@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A927996016A9CC9D9021B96'
        ],
        [
            'nome' => 'MARIA EDUARDA DA SILVA',
            'email' => 'facc.4042@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A927996016A9DF91FA60E84'
        ],
        [
            'nome' => 'Maria Teresa Saenz Surita Guimarães',
            'email' => 'teresasurita@prefeitura.boavista.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016A9E73A4C61BA9'
        ],
        [
            'nome' => 'Miguel Fontes',
            'email' => 'm.fontes@promundo.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA1F011256CB5'
        ],
        [
            'nome' => 'Marcela Cavallari Augusto',
            'email' => 'marcela.cavallari@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA23E954223D9'
        ],
        [
            'nome' => 'Alexandre Almeida da Silva',
            'email' => 'rederba@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA23248215175'
        ],
        [
            'nome' => 'Maria Amália da Silva Marques',
            'email' => 'amaliamarques@yahoo.com.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA23248215175'
        ],
        [
            'nome' => 'Angela Maria Gordilho Souza',
            'email' => 'amgs@ufba.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA28F87B47F8B'
        ],
        [
            'nome' => 'Heliana Faria Mettig Rocha',
            'email' => 'helianamettig@ufba.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA28F87B47F8B'
        ],
        [
            'nome' => 'Naia Alban Suarez',
            'email' => 'naialban@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA28F87B47F8B'
        ],
        [
            'nome' => 'Elisabete Monteiro',
            'email' => 'elisabete@institutochapada.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA293F42B1AB4'
        ],
        [
            'nome' => 'Mércia Gonzaga de Britto',
            'email' => 'projetos@cinemanosso.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA299003D36A4'
        ],
        [
            'nome' => 'Leonardo Duarte Pascoal',
            'email' => 'prefeito@esteio.rs.gov.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA2AC99112258'
        ],
        [
            'nome' => 'Mayhumi Kitagawa Costa Freitas',
            'email' => 'mayhumi.freitas@ccea.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA2E43EAA494D'
        ],
        [
            'nome' => 'Enerilda do Carmo Cunha',
            'email' => 'enseadadabaleia@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA3228E670EFA'
        ],
        [
            'nome' => 'Bruno Carvalho Cavalcante Rolim',
            'email' => 'bruno@ifc.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA70F058E4DFA'
        ],
        [
            'nome' => 'Diego Ramalho Freitas',
            'email' => 'diego@ifc.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA70F058E4DFA'
        ],
        [
            'nome' => 'Everton Kischlat',
            'email' => 'everton@ifc.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA70F058E4DFA'
        ],
        [
            'nome' => 'Olavo Pontes Santana',
            'email' => 'olavo@ifc.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA70F058E4DFA'
        ],
        [
            'nome' => 'Cláudia Glauciana Castro da Silva',
            'email' => 'claudiaglauciana@hotmail.com',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA7291C1462A7'
        ],
        [
            'nome' => 'Magali Nogueira Delfino Carmo',
            'email' => 'geed@prefeiturademossoro.com.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA7291C1462A7'
        ],
        [
            'nome' => 'Selma Andrade de Paula Bedaque',
            'email' => 'sbedaque@terra.com.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA7291C1462A7'
        ],
        [
            'nome' => 'Antonio Leomar Ferreira Soares',
            'email' => 'antonioleomar@ufcg.edu.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA814DA2722B6'
        ],
        [
            'nome' => 'Karina Rocha Leite',
            'email' => 'gentileza@argilando.org',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA820BFDC520E'
        ],
        [
            'nome' => 'Pedro Ronan Campos da Costa Marcondes',
            'email' => 'diretoria@argilando.org',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA820BFDC520E'
        ],
        [
            'nome' => 'Ana Beatriz Moser',
            'email' => 'abm@esporteeducacao.org.br',
            'cod_tecnologia_lumis' => '2C908A916A927996016AA91D5BB93E1F'
        ],
        [
            'nome' => 'Fernanda Rosas Pereira de Araujo',
            'email' => 'fernandarosas@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916AB16F24016AB28F0850735B'
        ],
        [
            'nome' => 'Luiz Cláudio Moura Santos',
            'email' => 'florestasdecomida@gmail.com',
            'cod_tecnologia_lumis' => '2C908A916AB16F24016AB28F0850735B'
        ]
    ];

    protected $import_data = [
        // 'nome' => '',
        // 'email' => '',
        // 'cod_tecnologia_lumis' => ''
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
        print "Inciando Script de Criação de usuários para o lançamento do beta\n\n";
        print " Exclua o bloqueio do script para prosseguir.";
        exit;
        $count_new = 0;
        foreach ($this->import_data as $arr_import) {

            // Busca a tecnologia social pelo código Lumis
            $socialtecnology_obj = SocialTecnology::where('cod_lumis', $arr_import['cod_tecnologia_lumis'])->first();

            $institution_id = null;
            if (!empty($socialtecnology_obj)) {
                $institution_id = $socialtecnology_obj->institution_id;
            } else {
                print "ERROR --- {$arr_import['cod_tecnologia_lumis']}\n";
            }


            $user_obj = User::where('email', $arr_import['email'])->first();

            if (!empty($user_obj)) {
                SocialTecnologyUser::where([
                    'user_id' => $user_obj->id,
                    'socialtecnology_id' => $socialtecnology_obj->id
                ])->delete();
            }

            if (empty($user_obj)) {

                $generated_random_password = Str::random(10);
                $hash_password = Hash::make($generated_random_password);

                $arr_import['nome'] = mb_convert_case($arr_import['nome'],MB_CASE_TITLE);

                // Gera url unica
                $arr_import["seo_url"] = \App\Helpers::slug($arr_import['nome']);
                $arr_import["seo_url"] = \App\Helpers::generate_unique_friendly_url($arr_import, new User);

                $user_obj = User::create([
                    'name' => $arr_import['nome'],
                    'email' => $arr_import['email'],
                    'seo_url' => $arr_import['seo_url'],
                    'institution_id' => $institution_id,
                    'password' => $hash_password,
                ]);
                $count_new++;

                print "\n\nUsuário criado:\n";
                if (!empty($user_obj->institution_id)) print "    -- Instituição: ".$user_obj->institution->institution_name."\n";
                print "    -- Nome: ".$user_obj->name."\n";
                print "    -- Seo Url: ".$user_obj->seo_url."\n";
                print "    -- E-mail: ".$user_obj->email."\n";
                print "    -- Senha: ".$generated_random_password."\n\n";

                // Envia notificação para o usuário
                $user_obj->notify(new SendUserEmail(
                    $user_obj->name,
                    $user_obj->email,
                    $generated_random_password,
                    (!empty($socialtecnology_obj) ? $socialtecnology_obj->socialtecnology_name : "")
                ));
            }

            print "Definindo responsáveis da TS - ".$socialtecnology_obj->socialtecnology_name."\n--------------------------------\n\n";
            SocialTecnologyUser::create([
                'user_id' => $user_obj->id,
                'socialtecnology_id' => $socialtecnology_obj->id
            ]);
        }

        print "Script Finalizado - Usuários Criados: ".$count_new."\n";
    }
}