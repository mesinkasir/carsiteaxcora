<?php

class pluginTailwindAbout extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'About',
			'text'=>''
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Label').'</label>';
		$html .= '<input name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$L->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('About').'</label>';
		$html .= '<textarea name="text" id="jstext">'.$this->getValue('text').'</textarea>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		$html  = '<div class="flex flex-col items-start">';
		$html .= '<dt class="mt-4 font-semibold text-white"><h3>'.$this->getValue('label').'</h3></dt>';
		$html .= '<dd class="mt-2 leading-7 text-gray-400">';
		$html .= html_entity_decode(nl2br($this->getValue('text')));
 		$html .= '</dd>';
 		$html .= '</div>';

		return $html;
	}
}