<?php 

/**
 * Controller which manages page transition
 * 
 * @author nagayasu
 *
 */
class sfFlow_Controller
{
  /**
   * @var sfFlow_Continue
   */
  private $continue;
  
  /**
   * @var array
   */
  private $stateMap = array();
  
  /**
   * Constructor
   * 
   * @param sfFlow_Continue    $continue
   * @return void
   */
  public function __construct($ns, $initState)
  {
    $continue = sfFlow_Continue::getInstance($ns);
    if ($continue->isInit()) {
      $continue->setState($initState);
    }
    
    $this->continue = $continue;
    $this->stateMap = array();
  }

  /**
   * Return template name
   * 
   * @return string
   */
  public function getTemplate()
  {
    $state = $this->continue->getState();
    if (!isset($this->stateMap[$state])) {
      throw new Exception(sprintf('The state [%s] is not registered.', $state));
    }
    
    return $this->stateMap[$state]->getTemplate();
  }
  
  /**
   * Execute controller
   * 
   * @return callable
   */
  public function execute(sfWebRequest $request)
  {
    $state = $this->continue->getState();
    if (!isset($this->stateMap[$state])) {
      throw new Exception(sprintf('The state [%s] is not registered.', $state));
    }
    
    foreach ($this->stateMap[$state] as $event => $callable) {
      if ($this->continue->happenEvent($event)) {
        if (is_callable($callable)) {
          call_user_func($callable, $request);
        }

        break;
      }
    }
    
    return $this->getContinue()->getState();
  }
  
  /**
   * Register event
   * 
   * @param $state    string
   * @param $event    string
   * @param $callback    mixed
   * @return void
   */
  public function addEvent($state, $event, $callback = null)
  {
    if (!isset($this->stateMap[$state])) {
      throw new Exception(sprintf('The state [%s] is not registered.', $state));
    }
    
    $this->stateMap[$state]->addEvent($event, $callback);
  }

  /**
   * Unregister event
   * 
   * @param $state    string
   * @param $event    string
   * @return void
   */
  public function removeEvent($state, $event)
  {
    if (!isset($this->stateMap[$state])) {
      throw new Exception(sprintf('The state [%s] is not registered.', $state));
    }
    
    $this->stateMap[$state]->removeEvent($event);
  }
  
  /**
   * Register state
   * 
   * @param $state
   * @param $template
   * @return void
   */
  public function addState($state, $template = null)
  {
    $this->stateMap[$state] = new sfFlow_State($state, $template);
  }
  
  /**
   * Unregister state
   * 
   * @param $state
   * @return void
   */
  public function removeState($state)
  {
    unset($this->stateMap[$state]);
  }
  
  public function getContinue()
  {
    return $this->continue;
  }
}
