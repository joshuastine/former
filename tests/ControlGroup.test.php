<?php
// Stub class for Buttons
class Buttons
{
  public static function normal($text)
  {
    return '<button type="button" class="btn">' .$text. '</button>';
  }
}

class ControlGroupTest extends FormerTests
{
  // Data providers ------------------------------------------------ /

  public function provideStates()
  {
    return array(
      array('error'),
      array('info'),
      array('success'),
      array('warning'),
    );
  }

  // Helpers ------------------------------------------------------- /

  public function createMatcher($state = null, $inlineHelp = null, $blockHelp = null)
  {
    if($state) $state = ' ' .$state;
    if($inlineHelp) $inlineHelp = '<span  class="help-inline">' .ucfirst($inlineHelp). '</span>';
    if($blockHelp)  $blockHelp  = '<p  class="help-block">' .ucfirst($blockHelp). '</p>';

    return
    '<div class="control-group' .$state. '">' .
      '<label for="foo" class="control-label">Foo</label>' .
      '<div class="controls">' .
        '<input type="text" name="foo" id="foo">' .
        $inlineHelp .
        $blockHelp .
      '</div>' .
    '</div>';
  }

  public function createPrependAppendMatcher($prepend = array(), $append = array())
  {
    foreach($prepend as $k => $p) if(!starts_with($p, '<button')) $prepend[$k] = '<span class="add-on">' .$p. '</span>';
    foreach($append as $k => $a)  if(!starts_with($a, '<button'))  $append[$k] = '<span class="add-on">' .$a. '</span>';

    $class = null;
    if($prepend) $class = "input-prepend";
    if($append) $class .= " input-append";


    return
    '<div class="control-group">' .
      '<label for="foo" class="control-label">Foo</label>' .
      '<div class="controls">' .
        '<div class="' .$class. '">'.
          join(null, $prepend).
          '<input type="text" name="foo" id="foo">' .
          join(null, $append).
          '</div>'.
      '</div>' .
    '</div>';
  }

  // Tests --------------------------------------------------------- /

  public function testOpen()
  {
    $control = Former::text('foo')->__toString();
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $control);
  }

  /**
   * @dataProvider provideStates
   */
  public function testChangeState($state)
  {
    $control = Former::text('foo')->state($state)->__toString();
    $matcher = $this->createMatcher($state);

    $this->assertEquals($matcher, $control);
  }

  public function testInlineHelp()
  {
    $control = Former::text('foo')->inlineHelp('foo')->__toString();
    $matcher = $this->createMatcher(null, 'foo');

    $this->assertEquals($matcher, $control);
  }

  public function testBlockHelp()
  {
    $control = Former::text('foo')->blockHelp('foo')->__toString();
    $matcher = $this->createMatcher(null, null, 'foo');

    $this->assertEquals($matcher, $control);
  }

  public function testBothHelps()
  {
    $control = Former::text('foo')->inlineHelp('foo')->blockHelp('foo')->__toString();
    $matcher = $this->createMatcher(null, 'foo', 'foo');

    $this->assertEquals($matcher, $control);
  }

  public function testPrepend()
  {
    $control = Former::text('foo')->prepend('@')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('@'));

    $this->assertEquals($matcher, $control);
  }

  public function testPrependMultiple()
  {
    $control = Former::text('foo')->prepend('@', '$')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('@', '$'));

    $this->assertEquals($matcher, $control);
  }

  public function testAppend()
  {
    $control = Former::text('foo')->append('@')->__toString();
    $matcher = $this->createPrependAppendMatcher(array(), array('@'));

    $this->assertEquals($matcher, $control);
  }

  public function testPrependAppend()
  {
    $control = Former::text('foo')->prepend('@')->append('@')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('@'), array('@'));

    $this->assertEquals($matcher, $control);
  }

  public function testPrependAppendMix()
  {
    $control = Former::text('foo')
      ->prepend('@', Buttons::normal('foo'))
      ->append('@', Buttons::normal('foo'))
      ->__toString();
    $matcher = $this->createPrependAppendMatcher(
      array('@', '<button type="button" class="btn">foo</button>'),
      array('@', '<button type="button" class="btn">foo</button>'));

    $this->assertEquals($matcher, $control);
  }

  public function testPrependButton()
  {
    $control1 = Former::text('foo')->prepend(Buttons::normal('Submit'))->__toString();
    $control2 = Former::text('foo')->prepend('<button type="button" class="btn">Submit</button>')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('<button type="button" class="btn">Submit</button>'));

    $this->assertEquals($matcher, $control1);
    $this->assertEquals($matcher, $control2);
  }

  public function testAllTheThings()
  {
    $control = Former::text('foo')
      ->state('error')
      ->inlineHelp('foo')
      ->blockHelp('bar')
      ->prepend('@', '$', Buttons::normal('foo'))
      ->append('@', '$', Buttons::normal('foo'))
      ->__toString();
    $matcher =
    '<div class="control-group error">'.
      '<label for="foo" class="control-label">Foo</label>'.
      '<div class="controls">'.
        '<div class="input-prepend input-append">'.
            '<span class="add-on">@</span>'.
            '<span class="add-on">$</span>'.
            '<button type="button" class="btn">foo</button>'.
            '<input type="text" name="foo" id="foo">'.
            '<span class="add-on">@</span>'.
            '<span class="add-on">$</span>'.
            '<button type="button" class="btn">foo</button>'.
          '</div>'.
        '<span  class="help-inline">Foo</span>'.
        '<p  class="help-block">Bar</p>'.
      '</div>'.
    '</div>';

    $this->assertEquals($matcher, $control);
  }

}