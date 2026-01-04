<?php

declare(strict_types=1);

use Laravolt\SemanticForm\Elements\File;

class FileTest extends PHPUnit\Framework\TestCase
{
    public function test_render_file_input()
    {
        $file = new File('article');
        $expected = '<input type="file" name="article">';
        $result = $file->render();
        $this->assertEquals($expected, $result);
    }
}
