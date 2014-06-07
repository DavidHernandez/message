<?php

/**
 * @file
 * Contains \Drupal\message\Form\MessageTypeConfigTranslationAddForm.
 */

namespace Drupal\message\Form;
use Drupal\message\Controller\MessageController;
use Drupal\message\FormElement\MessageTypeMultipleTextField;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a form for adding configuration translations.
 */
class MessageTypeConfigTranslationAddForm extends MessageTypeConfigTranslationBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'message_type_config_translation_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, Request $request = NULL, $plugin_id = NULL, $langcode = NULL) {
    $form = parent::buildForm($form, $form_state, $request, $plugin_id, $langcode);

    // Get the name of the message type.
    $names = $this->mapper->getConfigNames();
    $name = reset($names);
    $translation = &$form['config_names'][$name]['text']['translation'];

    $configs = $form_state['config_translation_mapper']->getConfigData();
    $config = reset($configs);
    $multiple = new MessageTypeMultipleTextField(MessageController::MessageTypeLoad($config['type']), array(get_class($this), 'addMoreAjax'));
    $multiple->textField($translation, $form_state);

    $form['#title'] = $this->t('Add @language translation for %label', array(
      '%label' => $this->mapper->getTitle(),
      '@language' => $this->language->name,
    ));
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);
    drupal_set_message($this->t('Successfully saved @language translation.', array('@language' => $this->language->name)));
  }

  /**
   * Ajax callback for the "Add another item" button.
   *
   * This returns the new page content to replace the page content made obsolete
   * by the form submission.
   */
  public static function addMoreAjax(array $form, array $form_state) {
    $configs = $form_state['config_translation_mapper']->getConfigData();
    $config = reset($configs);
    return $form['config_names']['message.type.' . $config['type']]['text']['translation']['text'];
  }
}