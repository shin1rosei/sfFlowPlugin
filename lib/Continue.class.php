<?php

/**
 * State management class to manage page transition. 
 * 
 * @author sei
 */
class sfFlow_Continue
{
  private $state  = null;

  private $flowId = null;

  private $ns     = null;

  private $attributeHolder = null;

  private $init   = null;
  
  /**
   * Constructor
   * 
   * @param string    $ns
   * @param string    $flowId
   * @return void
   */
  private function __construct($ns, $flowId = null)
  {
    $this->ns = $ns;
    $this->flowId = ($flowId) ? $flowId : self::getRandomString();
    $this->attributeHolder = new sfParameterHolder();
  }

  public function setInit($init)
  {
    if (!is_bool($init)) {
      throw new Exception('$init should be boolean.');
    }
    
    $this->init = $init;
  }

  public function isInit()
  {
    return $this->init;
  }

  /**
   * Return the specific instance
   * 
   * @param $ns
   * @return sfFlow_Continue
   */
  public static function getInstance($ns)
  {
    $flowId   = sfContext::getInstance()->getRequest()->getParameter("flow_id");
    $instance = sfContext::getInstance()->getUser()->getAttribute("flowContinue", null, $ns ."/" . $flowId);

    if ($instance == null) {
      $instance = new self($ns, $flowId);
      sfContext::getInstance()->getResponse()->setHttpHeader('Cache-Control', "");
      sfContext::getInstance()->getResponse()->setHttpHeader('Pragma', "");
      $instance->setInit(true);
    } else {
      $instance->setInit(false);
    }
    
    return $instance;
  }

  /**
   * Return whether the given event was happen. 
   * 
   * @param $eventName
   * @return bool
   */
  public function happenEvent($eventName)
  {
    $request = sfContext::getInstance()->getRequest();

    if ($request->getParameter($eventName) != null ||
    $request->getParameter($eventName . "_x") !== null ||
    $request->getParameter($eventName . "_y") !== null) {
      return true;
    }

    return false;
  }

  /**
   * Get state
   * 
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }

  /**
   * Set state
   * 
   * @param $state
   * @return void
   */
  public function setState($state)
  {
    $this->state = $state;
    $this->save();
  }

  /**
   * Return flow_id
   * 
   * @return string
   */
  public function getFlowId()
  {
    return $this->flowId;
  }

  /**
   * Clear session, and remove namespace
   * 
   * @return void
   */
  public function remove()
  {
    $this->clearAttribute();
    sfContext::getInstance()->getUser()->getAttributeHolder()->removeNameSpace($this->ns . "/" . $this->flowId);
  }

  /**
   * Set attribute key/value
   * 
   * @param string    $key
   * @param mixed    $attribute
   * @return void
   */
  public function setAttribute($key, $attribute)
  {
    $this->attributeHolder->set($key, $attribute);
    $this->save();
  }

  /**
   * Deletes the key, and return the maintained value.
   * 
   * @param $key
   * @return mixed
   */
  public function removeAttribute($key)
  {
    $retval = $this->attributeHolder->remove($key);
    $this->save();

    return $retval;
  }

  /**
   * Return the value corresponding to the key.
   * 
   * @param string    $key
   * @param mixed    $default
   * @return mixed
   */
  public function getAttribute($key, $default = null)
  {
    return $this->attributeHolder->get($key, $default);
  }

  /**
   * Return the presence of the key.
   * 
   * @param string    $key
   * @return bool
   */
  public function hasAttribute($key)
  {
    return $this->attributeHolder->has($key);
  }

  /**
   * Clear attribute holder
   * 
   * @return void
   */
  public function clearAttributes()
  {
    $this->attributeHolder->clear();
    $this->save();
  }

  /**
   * Save the session. 
   * 
   * @return void
   */
  private function save()
  {
    sfContext::getInstance()->getUser()->setAttribute("flowContinue", $this, $this->ns . "/" . $this->flowId);
  }

  /**
   * Generate a random character string. 
   * 
   * @param int    $nLengthRequired
   * @return string
   */
  private static function getRandomString($nLengthRequired = 8){
    $sCharList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
    mt_srand();

    $sRes = "";
    for ($i=0; $i<$nLengthRequired; $i++) {
      $sRes .= $sCharList{mt_rand(0, strlen($sCharList) - 1)};
    }

    return $sRes;
  }
}