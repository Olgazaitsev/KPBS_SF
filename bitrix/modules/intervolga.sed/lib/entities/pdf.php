<?php
namespace Intervolga\Sed\Entities;

use Dompdf\Dompdf;
use Dompdf\Options;

require(__DIR__ . '/../../external_lib/dompdf/autoload.inc.php');

class PDF extends Dompdf
{
    public function __construct($options)
    {
        $options = new Options();
        $options->set('defaultFont', 'times');

        parent::__construct($options);
    }

    public function stream($filename = 'document', $options = array('Attachment' => false))
    {
        $this->setPaper('A4');
        $this->render();
        parent::stream($filename, $options);
    }
}