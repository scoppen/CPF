<?php

include_once("CPF/php/HTMLFormatter.class.php");
include_once("CPF/php/Interfaces.php");

class SimpleInsertAdapter extends HTMLFormatter implements IPageDBAdapter
{
  private $mPageMgr;
  private $mDatabase;
  private $mTableName;
  private $mFormName;
  private $mFormElements;
  private $mSubmitCommand;
  private $mButtonLabel;
  private $mRowId;
  private $mElementAttributes;
  private $mElementExtras;
  private $mElementArrays;
  private $mArrayAttributes;
  
  public function __construct(PageManager $pageMgr)
  {
    $this->mPageMgr = $pageMgr;
    $this->mDatabase = "";
    $this->mTableName = "";
    $this->mFormName = "";
    $this->mFormElements = array();
    $this->mSubmitCommand = "";
    $this->mButtonLabel= "Insert";
    $this->mRowId = 0;
    $this->mElementAttributes = array();
    $this->mElementExtras = array();
    $this->mElementArrays = array();
    $this->mArrayAttributes = array();
  }

  private function __clone() { }

  public function getLabel($str)
  {
    $arr = explode('_', $str, 2);
    return $arr[1];
  }

  public function getGroup($str)
  {
    $arr = explode('_', $str, 2);
    return $arr[0];
  }

  protected function getFormElements()
  {
    return $this->mFormElements;
  }

  protected function getElementAttributes()
  {
    return $this->mElementAttributes;
  }

  protected function getElementArrays()
  {
    return $this->mElementArrays;
  }

  protected function getElementExtras()
  {
    return $this->mElementExtras;
  }

  protected function setElementAttribute($key, $name, $attribute)
  {
    $this->mElementAttributes[$key][$name] = $attribute;
  }

  protected function setElementArray($key, $name, $array)
  {
    $this->mElementArrays[$key][$name] = $array;
  }

  protected function setElementExtra($key, $name, $extra)
  {
    $this->mElementExtras[$key][$name] = $extra;
  }

  protected function setFormElementValue($key, $value)
  {
    if (array_key_exists($key, $this->mFormElements))
    {
      $formElement = $this->mFormElements[$key];
      switch ($formElement)
      {
      case 'input':
        switch ($this->mElementAttributes[$key]['type'])
        {
        case 'checkbox':
          if ($value != 0)
            $this->setElementAttribute($key, 'checked', 'true');
          break;

        default:
          $this->setElementAttribute($key, 'value', $value);
          break;
        }
        break;

      case 'select':
        if ($value != 0)
        {
          if (!array_key_exists($key, $this->mElementAttributes))
            $this->mElementAttributes[$key] = array();

          $this->setElementAttribute($key, 'selected', $value);
        }
        break;

      case 'textarea':
        $this->setElementArray($key, 'value', $value);
        break;
      }
    }
  }

  public function setScriptProperties($formName, $submitCommand)
  {
    $this->mFormName = $formName;
    $this->mSubmitCommand = $submitCommand;
  }

  protected function setButtonLabel($label)
  {
    $this->mButtonLabel = $label;
  }

  protected function setRowId($rowId)
  {
    $this->mRowId = $rowId;
  }

  public function configure(IDatabase $database, $tableName)
  {
    $this->mDatabase = $database;
    $this->mTableName = $tableName;

    $datatypes = $database->getTableDataTypes($tableName);
    $comments = $database->getTableColumnComments($tableName);
    $isNullables = $database->getTableIsNullables($tableName);
    $octetLengths = $database->getTableCharacterOctetLengths($tableName);
    $maxLengths = $database->getTableCharacterMaxLengths($tableName);
    
    foreach ($datatypes as $key => &$value)
    {
      if (($key == "_id") || ($key == '_timestamp'))
        continue;

      switch ($value)
      {
      case "varchar":
        $size = $this->mPageMgr->getMaxTextInputSize();
        if ($octetLengths[$key] <= $size)
        {
          $this->mFormElements[$key] = 'input';
          $this->mElementAttributes[$key] = array();
          $this->setElementAttribute($key, 'type', 'text');
          $this->setElementAttribute($key, 'size', $octetLengths[$key]);
          $this->setElementAttribute($key, 'maxlength', $maxLengths[$key]);
        }
        else
        {
          $this->mFormElements[$key] = 'textarea';
          $this->mElementAttributes[$key] = array();
          $this->setElementAttribute($key, 'cols', $size);
          $this->setElementAttribute($key, 'rows',
              min(8, (int)($maxLengths[$key] / $size)));
          $this->mElementArrays[$key] = array();
        }
        break;

      case "enum":
        $this->mFormElements[$key] = 'select';
        $this->mElementArrays[$key] = array();
        $this->setElementArray($key, 'option',
            $database->getTableColumnTypeSchema($tableName, $key)); 
        break;

      case "date":
        $this->mFormElements[$key] = 'input';
        $this->mElementAttributes[$key] = array();
        $this->setElementAttribute($key, 'type', 'text');
        $this->setElementAttribute($key, 'size', 10);
        $this->setElementAttribute($key, 'maxlength', 10);
        $this->setElementAttribute($key, 'onKeyPress',
            'return dateOnly(this,event)');
        break;

      case "tinyint":
        $this->mFormElements[$key] = 'input';
        $this->mElementAttributes[$key] = array();
        $this->setElementAttribute($key, 'type', 'checkbox');
        break;

      case "int":
        $this->mElementAttributes[$key] = array();
        $this->parseElementArray($database, $comments, $key);
        if (array_key_exists($key, $this->mElementArrays))
          $this->mFormElements[$key] = 'select';
        else
        {
          $this->mFormElements[$key] = 'input';
          $this->setElementAttribute($key, 'type', 'text');
          $this->setElementAttribute($key, 'size', 5);
          $this->setElementAttribute($key, 'maxlength', 8);
          $this->setElementAttribute($key, 'onKeyPress',
              'return numbersOnly(this,event)');
        }
        break;
      }

      $this->parseElementExtras($database, $comments, $key);
    }
  }

  private function parseElementExtras($database, $comments, $key)
  {
    if (!isset($comments[$key]) || ($comments[$key][0] != '#'))
      return;

    $commentParts = explode(":", $comments[$key]);
    if (count($commentParts) <= 1)
      return;

    $this->mElementExtras[$key] = array();
    switch (substr($commentParts[0], 1))
    {
    case "units":
      $units = explode(":", $comments[$key]);
      if (count($units) == 2)
        $this->setElementExtra($key, 'units', trim($units[1]));
      break;
    }
  }

  private function parseElementArray($database, $comments, $key)
  {
    if (!isset($comments[$key]) || ($comments[$key][0] != '#'))
      return;

    $commentParts = explode(":", $comments[$key]);
    if (count($commentParts) <= 1)
      return;

    switch (substr($commentParts[0], 1))
    {
    case "reftable":
      $linkParts = explode(".", trim($commentParts[1]));
      switch (count($linkParts))
      {
      case 1:
      default:
        $tableName = trim($linkParts[0], "\x60");
        $tmp = $database->getTableEnumerationArray($tableName);
        if (is_array($tmp))
        {
          $this->mElementArrays[$key] = array();
          $this->setElementArray($key, 'option', $tmp);
        }
      }
      break;
    }
  }

  public function draw($cssClass = "")
  {
    //echo print_r($this->mFormElements)."<br />";
    //echo print_r($this->mElementAttributes)."<br />";
    //echo print_r($this->mElementArrays)."<br />";
    //echo print_r($this->mElementExtras)."<br />";

    $prevGroup = '';
    
    echo "<form name='".$this->mFormName."'>"
        ."<fieldset style='margin: 1em;'>"
        ."<legend>".urldecode($this->mTableName)."</legend>"
        ."<table>";
    foreach ($this->mFormElements as $elem => &$elemValue)
    {
      $label = $this->getLabel($elem);
      $group = $this->getGroup($elem);

      echo "<tr>";
      if ($group != $prevGroup)
      {
        if ($prevGroup != '')
        {
          echo "</table></fieldset></td></tr><tr>";
        }
        if ($group != '')
        {
          echo "<td colspan=2><fieldset style='margin: 1em 0em;'>"
              ."<legend>".urldecode($group)."</legend>"
              ."<table><tr>";
        }

        $prevGroup = $group;
      }

      echo "<td><b>".urldecode($label)."</b></td>";
      echo "<td><".$elemValue." name='".$elem."' "
          ."id='".$this->mFormName."_".$elem."' ";
      if (array_key_exists($elem, $this->mElementAttributes))
      {
        foreach ($this->mElementAttributes[$elem] as $attr => &$attrValue)
        {
          echo $attr."='".$attrValue."' ";
        }
      }

      if (array_key_exists($elem, $this->mElementArrays))
      {
        echo ">";
        foreach ($this->mElementArrays[$elem] as $arry => &$arryValue)
        {
          switch ($arry)
          {
          default:
            foreach ($arryValue as $arryKey =>&$arryKeyValue)
              echo "<".$arry.">".$arryKeyValue."</".$arry.">";
            break;

          case "value":
            echo $arryValue;
            break;

          case "option":
            foreach ($arryValue as $arryKey =>&$arryKeyValue)
            {
              echo "<".$arry." value=".$arryKey;
              if (array_key_exists('selected', $this->mElementAttributes[$elem]) &&
                  ($this->mElementAttributes[$elem]['selected'] == $arryKey))
                echo " selected";

              echo ">".$arryKeyValue."</".$arry.">";
            }
            break;
          }
        }
        echo "</".$elemValue.">";
      }
      else
        echo " />";
      
      if (array_key_exists($elem, $this->mElementExtras))
      {
        if (array_key_exists("units", $this->mElementExtras[$elem]))
          echo "&nbsp;[".$this->mElementExtras[$elem]['units']."]";
      }
      echo "</td></tr>";
    }

    if ($prevGroup != '')
    {
      echo "</table></fieldset></td></tr>";
    }

    $dbProps = array();
    $dbProps['database'] = $this->mDatabase->getDatabase();
    $dbProps['table'] = $this->mTableName;
    $dbProps['form'] = $this->mFormName;
    if ($this->mRowId > 0)
      $dbProps['row_id'] = $this->mRowId;

    echo "<tr><td colspan=2><hr></td></tr>"
        ."<tr><td style='text-align: center;'>"
        ."<input type=button value='".$this->mButtonLabel."' "
        ."onclick='".$this->mSubmitCommand."(".json_encode($dbProps).","
        .json_encode($this->mFormElements)
        .")'/></td>";

    echo "<td><div id='insertadapter_result'></div></td></tr>";
    echo "</table></fieldset>";

    echo "</form>";
  } 
}

?>
