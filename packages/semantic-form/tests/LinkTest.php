<?php

declare(strict_types=1);

class LinkTest extends PHPUnit\Framework\TestCase
{
    public function test_render_basic_link()
    {
        $link = new Laravolt\SemanticForm\Elements\Link('Cancel', 'http://back.test');
        $expected = '<a class="ui basic button" themed href="http://back.test">Cancel</a>';
        $result = $link->render();

        $this->assertEquals($expected, $result);
    }
}
