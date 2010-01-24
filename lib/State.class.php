<?php

/**
 * Event managed object maintained by controller
 * 
 * @author nagayasu
 *
 */
class sfFlow_State extends ArrayIterator
{
  /**
   * @var string    State name
   */
  private $state;
  
  /**
   * @var string    Template name corresponding to state
   */
  private $template;

  /**
   * @var ArrayObject
   */
  private $eventMap;
  
  /**
   * @var ArrayIterator
   */
  private $eventMapIterator;
  
  /**
   * Constructor
   * 
   * @param $state
   * @param $template
   * @return void
   */
  public function __construct($state, $template = null)
  {
    // When omitting, set the same name.
    if (!$template) {
      $template = $state;
    }

    $this->state    = $state;
    $this->template = $template;
    $this->eventMap = new ArrayObject(array());
    $this->eventMapIterator = $this->eventMap->getIterator();
  }

  /**
   * getter $template
   * 
   * @return string
   */
  public function getTemplate()
  {
    return $this->template;
  }

  /**
   * getter $state
   * 
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }

  /**
   * Register event
   * 
   * @param $event
   * @param $callable
   * @return void
   */
  public function addEvent($event, $callable = null)
  {
    $this->eventMap[$event] = $callable;
  }
  
  /**
   * Unregister event
   * 
   * @param $event
   * @return void
   */
  public function removeEvent($event)
  {
    unset($this->eventMap[$event]);
  }
  
  public function current()
  {
    return $this->eventMapIterator->current();
  }
  public function key()
  {
    return $this->eventMapIterator->key();
  }
  public function next()
  {
    return $this->eventMapIterator->next();
  }
  public function rewind()
  {
    return $this->eventMapIterator->rewind();
  }
  public function seek($pos)
  {
    return $this->eventMapIterator->seek(pos);
  }
  public function valid()
  {
    return $this->eventMapIterator->valid();
  }
}
