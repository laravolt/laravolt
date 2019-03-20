<?php

use Laravolt\SemanticForm\Elements\File;

class FileTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderFileInput()
    {
        $file = new File('article');
        $expected = '<input type="file" name="article">';
        $result = $file->render();
        $this->assertEquals($expected, $result);
    }
}
