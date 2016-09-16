<?php

namespace Drupal\downloads_widget\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class DownloadsWidgetForm.
 *
 * @package Drupal\downloads_widget\Form
 */
class DownloadsWidgetForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'DownloadsWidgetForm';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['filename'] = array(
      '#type' => 'select',
      '#title' => $this->t('Filename'),
      '#description' => $this->t('Select the file that you want to download'),
      '#options' => $this->getDocuments(),
      '#size' => 1,
      '#required' => TRUE,
    );
	
	$form['password'] = array (
		'#type'=>'textfield',
		'#title'=>$this->t('password'),
		'#size'=>60,
		'#maxlength'=>128,
		'#required'=>TRUE,
	);
	
	$form['submit'] = [
		'#type' => 'submit',
		'#value' => t('Download a file'),
	];

    return $form;
  }
  
  public function validateForm(array &$form, FormStateInterface $form_state) {
	  parent::validateForm($form, $form_state);
	  $uri = $form_state->getValue('filename');
	  
	  $query = \Drupal::database()->select('file_managed','f');
	  $query->condition('f.uri',$uri);
	  $query->fields('f',array('fid'));
	  $fid = $query->execute()->fetchField();
	  
	  $query = \Drupal::database()->select('file__field_password','ffpp');
	  
	  $query->condition('ffpp.entity_id',$fid);
	  $query->fields('ffpp',array('field_password_value'));
	  $password = $query->execute()->fetchField();
	  
	  if ($password && $password != $form_state->getValue('password')){
		  $form_state->setErrorByName('password',t('invalid password'));
	  }
	  
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	  
	/*  
	foreach($form_state->getValues() as $key => $value){
		drupal_set_message($key.': '.$value);
	}
	*/
	
	$uri = $form_state->getValue('filename');
	drupal_set_message($uri);
	$response = new BinaryFileResponse($uri);
	
	$response->setContentDisposition('attachment');
	
	$form_state->setResponse($response);
  }
  
  public function getDocuments() {
		$documents_query = \Drupal::database()->select('file_managed','f')
		  ->condition('f.type','image')
		  ->fields('f',array('filename','uri'))
		  ->execute()
		  ->fetchAll();
		$documents = array();
		foreach($documents_query as $document){
			$documents[$document->uri] = $document->filename;
		}
		
		//$documents = array('download1' => $this->t('download1'), 'download2' => $this->t('download2'));
		
		return $documents;
	}

}

