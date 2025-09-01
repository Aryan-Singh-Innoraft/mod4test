<?php

namespace Drupal\site_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Site API settings for this site.
 */
class ApiForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'site_api_api';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['site_api.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date range'),
      '#default_value' => $this->config('site_api.settings')->get('date'),
    ];
    $form['tags'] = [
      '#type' => 'number',
      '#title' => $this->t('Tags'),
      '#default_value' => $this->config('site_api.settings')->get('tags'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('site_api.settings')
      ->set('date', $form_state->getValue('date'))
      ->set('tags', $form_state->getValue('tags'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
