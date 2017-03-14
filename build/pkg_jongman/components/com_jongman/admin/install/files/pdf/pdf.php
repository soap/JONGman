<?php
defined('JPATH_BASE') or die();
 
require_once(JPATH_LIBRARIES .'/joomla/document/html/html.php');
jimport('tcpdf.tcpdf');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JDocumentPdf extends JDocumentHTML
{
	private $pdf 			= null;
	
	public $_type 			= 'pdf';
	public $_header			= null;
	public $name			= 'JONGmanPdf';
	
	// PDF settings
	public $orientation 	= 'P';
	public $pageFormat 		= 'A4';
	public $unit 			= 'mm';
	public $leftMargin 		= 10;
	public $rightMargin 	= 10;
	public $topMargin 		= 10;
	public $bottomMargin 	= 10;
	
	public function __construct($options=array())
	{
		parent::__construct($options);
		
		//set mime type
		$this->_mime = 'application/pdf';
		
		//set document type
		$this->_type = 'pdf';
		$this->_caching = null;
		
		$this->initTCPDF();
	}
	
	
	protected function initTCPDF()
	{
		$file = JPATH_LIBRARIES .'/tcpdf/tcpdf.php';
		if (!JFile::exists($file))
		{
			return false;
		}
		$l = Array();
		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'ltr';
		$l['a_meta_language'] = 'th';
		$l['w_page'] = 'page';
		
		$this->pdf = new TCPDF($this->orientation, $this->unit, $this->pageFormat, true, 'UTF-8', false);	
		$this->pdf->SetCreator('Joomla');
		$this->pdf->SetAuthor("JONGman Reservation System");
		$this->pdf->SetTitle($this->getTitle());
		$this->pdf->SetSubject("Reservation Data");
		$this->pdf->SetKeywords("JONGman, Reservation System, Joomla, PDF");
		
		// remove default header/footer
		$this->pdf->setPrintHeader(false);
		$this->pdf->setPrintFooter(false);
		
		//set margins
		$this->pdf->SetMargins($this->leftMargin, $this->topMargin, $this->rightMargin);
		
		//set auto page breaks
		$this->pdf->SetAutoPageBreak(true, $this->bottomMargin);
		
		//set image scale factor
		$this->pdf->setImageScale(2);
		
		$this->pdf->setCellHeightRatio(1.2);
		
		//set some language-dependent strings
		$this->pdf->setLanguageArray($l);
		
		//initialize document
		$this->pdf->SetFont("thsarabun", "", 12, true);
	} 
	
	/**
	 * Returns the document name
	 * @return	string
	 */
	
	public function getName()
	{
		return $this->name;
	}
	
	function setHeader($text) {
		$this->_header = $text;
	}
	
	function getHeader() {
		return $this->_header;
	}
	
	
	public function setHeadData($data)
	{
		if (empty($data) || !is_array($data)) {
			return;
		}
	
		$this->title		= (isset($data['title']) && !empty($data['title'])) ? $data['title'] : $this->title;
		$this->description	= (isset($data['description']) && !empty($data['description'])) ? $data['description'] : $this->description;
		$this->link			= (isset($data['link']) && !empty($data['link'])) ? $data['link'] : $this->link;
		$this->_metaTags	= (isset($data['metaTags']) && !empty($data['metaTags'])) ? $data['metaTags'] : $this->_metaTags;
		$this->_links		= array();
		$this->_styleSheets	= array();
		$this->_style		= array();
		$this->_scripts		= array();
		$this->_script		= array();
		$this->_custom		= array();
	}
	
	/**
	 * Render the document.
	 * @access public
	 * @param boolean 	$cache		If true, cache the output
	 * @param array		$params		Associative array of attributes
	 * @return	string
	 */
	
	public function render($cache = false, $params = array())
	{	
		$pdf = $this->pdf;
		$pdf->setHeaderData('' , 0, $this->getTitle(), $this->getHeader());
		$pdf->SetTextColor(100, 100, 100);
		
		$data = parent::render();
		$data = $this->getBuffer('component');
		//$this->fullPaths($data);

		$data = str_replace(array(utf8_encode(chr(11)), utf8_encode(chr(160))), ' ', $data);
		$pdf->AddPage();
		
		$pdf->writeHTML($data, true, false, true, false, '');

		JResponse::setHeader('Content-type', 'application/pdf', true);// Because of cache
		JResponse::setHeader('Content-disposition', 'inline; filename="'.$this->getName().'.pdf"', true);
		return $pdf->Output($this->getName(), 'I');
	}
	

	/**
	 * (non-PHPdoc)
	 * @see JDocumentHTML::getBuffer()
	 */
	
	 public function getBuffer($type = null, $name = null, $attribs = array())
	{
		if ($type == 'head' || $type == 'component')
		{
			return parent::getBuffer($type, $name, $attribs);
		}
		else
		{
			return '';
		}
	}
	
	/**
	 * parse relative images a hrefs and style sheets to full paths
	 * @param	string	&$data
	 */
	
	private function fullPaths(&$data)
	{
		$data = str_replace('xmlns=', 'ns=', $data);
		libxml_use_internal_errors(true);
		try
		{
			$ok = new SimpleXMLElement($data);
			if ($ok)
			{
				$uri = JUri::getInstance();
				$base = $uri->getScheme() . '://' . $uri->getHost();
				$imgs = $ok->xpath('//img');
				foreach ($imgs as &$img)
				{
					if (!strstr($img['src'], $base))
					{
						$img['src'] = $base . $img['src'];
					}
				}
				//links
				$as = $ok->xpath('//a');
				foreach ($as as &$a)
				{
					if (!strstr($a['href'], $base))
					{
						$a['href'] = $base . $a['href'];
					}
				}
	
				// css files.
				$links = $ok->xpath('//link');
				foreach ($links as &$link)
				{
					if ($link['rel'] == 'stylesheet' && !strstr($link['href'], $base))
					{
						$link['href'] = $base . $link['href'];
					}
				}
				$data = $ok->asXML();
			}
		}catch (Exception $err)
		{
			//oho malformed html - if we are debugging the site then show the errors
			// otherwise continue, but it may mean that images/css/links are incorrect
			$errors = libxml_get_errors();
			if (JDEBUG)
			{
				echo "<pre>";print_r($errors);echo "</pre>";
				exit;
			}
		}
	
	}

}