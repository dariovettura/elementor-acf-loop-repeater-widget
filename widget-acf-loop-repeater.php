<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Plugin;

if ( ! class_exists( '\Elementor\Modules\NestedElements\Base\Widget_Nested_Base' ) ) {
    return;
}

class Elementor_Acf_Loop_Repeater_Widget extends Widget_Nested_Base {

    public function get_name() {
        return 'acf-loop-repeater';
    }

    public function get_title() {
        return __( 'ACF Loop Repeater', 'acf-loop-repeater' );
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    public function get_keywords() {
        return [ 'acf', 'repeater', 'loop', 'grid' ];
    }

    protected function is_dynamic_content(): bool {
        return false;
    }

    public function get_style_depends(): array {
        return [ 'widget-nested-accordion' ];
    }

    public function show_in_panel(): bool {
        return Plugin::$instance->experiments->is_feature_active( 'nested-elements', true );
    }

    public function has_widget_inner_wrapper(): bool {
        return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
    }

    protected function get_html_wrapper_class() {
        return 'elementor-widget-n-accordion';
    }

    protected function item_content_container( int $index ) {
        return [
            'elType' => 'container',
            'settings' => [
                '_title' => sprintf(
                    /* translators: %d: Item index. */
                    __( 'item #%d', 'elementor' ),
                    $index
                ),
                'content_width' => 'full',
            ],
        ];
    }

    protected function get_default_children_elements() {
        return [
            $this->item_content_container( 1 ),
            $this->item_content_container( 2 ),
        ];
    }

    protected function get_default_repeater_title_setting_key() {
        return 'tab_title';
    }

    protected function get_default_children_title() {
        /* translators: %d: Item index. */
        return esc_html__( 'Item #%d', 'elementor' );
    }

    protected function get_default_children_placeholder_selector() {
        return '.e-n-accordion';
    }

    protected function get_default_children_container_placeholder_selector() {
        return '.e-n-accordion-item';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function get_initial_config(): array {
        return array_merge(
            parent::get_initial_config(),
            [
                'support_improved_repeaters' => true,
                'target_container' => [ '.e-n-accordion' ],
                'node' => 'details',
                'is_interlaced' => true,
            ]
        );
    }

    /**
     * Resolve the current post ID in Elementor Loop/Grid contexts.
     *
     * @return int
     */
    protected function get_context_post_id( array $settings = [] ) {
        if ( isset( $settings['post_id_source'] ) && 'manual' === $settings['post_id_source'] ) {
            $manual_post_id = ! empty( $settings['manual_post_id'] ) ? (int) $settings['manual_post_id'] : 0;
            if ( $manual_post_id > 0 ) {
                return $manual_post_id;
            }
        }

        $post_id = 0;

        if ( isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof \WP_Post ) {
            $post_id = (int) $GLOBALS['post']->ID;
        }

        if ( empty( $post_id ) ) {
            $post_id = (int) get_the_ID();
        }

        if ( empty( $post_id ) ) {
            $post = get_post();
            if ( $post instanceof \WP_Post ) {
                $post_id = (int) $post->ID;
            }
        }

        if ( empty( $post_id ) ) {
            $queried_object_id = get_queried_object_id();
            if ( ! empty( $queried_object_id ) ) {
                $post_id = (int) $queried_object_id;
            }
        }

        return (int) $post_id;
    }

  
  protected function register_controls() {

	      $this->start_controls_section(
      'content_section',
      [
          'label' => __( 'Impostazioni Repeater', 'acf-loop-repeater' ),
          'tab'   => Controls_Manager::TAB_CONTENT,
      ]
  );

  // Impostazioni per la chiave del campo ripetitore ACF
  $this->add_control(
      'acf_field_key',
      [
          'label'       => __( 'Chiave del campo ripetitore ACF', 'acf-loop-repeater' ),
          'type'        => Controls_Manager::TEXT,
          'default'     => 'faq',
          'description' => __( 'Inserisci la chiave del campo ripetitore ACF.', 'acf-loop-repeater' ),
      ]
  );

  $this->add_control(
      'question_field_key',
      [
          'label'       => __( 'Nome sotto-campo domanda (repeater item)', 'acf-loop-repeater' ),
          'type'        => Controls_Manager::TEXT,
          'default'     => 'titolo_faq',
          'description' => __( "Inserisci il 'name' del sotto-campo ACF che contiene la domanda dentro ogni elemento del repeater.", 'acf-loop-repeater' ),
      ]
  );

  $this->add_control(
      'answer_field_key',
      [
          'label'       => __( 'Nome sotto-campo risposta (repeater item)', 'acf-loop-repeater' ),
          'type'        => Controls_Manager::TEXT,
          'default'     => 'testo_faq',
          'description' => __( "Inserisci il 'name' del sotto-campo ACF che contiene la risposta dentro ogni elemento del repeater.", 'acf-loop-repeater' ),
      ]
  );

  $this->add_control(
      'post_id_source',
      [
          'label' => __( 'Sorgente Post ID', 'acf-loop-repeater' ),
          'type' => Controls_Manager::SELECT,
          'default' => 'auto',
          'options' => [
              'auto' => __( 'Automatico (contesto corrente)', 'acf-loop-repeater' ),
              'manual' => __( 'Manuale', 'acf-loop-repeater' ),
          ],
      ]
  );

  $this->add_control(
      'manual_post_id',
      [
          'label' => __( 'Post ID manuale', 'acf-loop-repeater' ),
          'type' => Controls_Manager::NUMBER,
          'min' => 1,
          'dynamic' => [
              'active' => true,
          ],
          'condition' => [
              'post_id_source' => 'manual',
          ],
      ]
  );

  $this->end_controls_section();
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Accordion', 'elementor' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Accordion Title', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label' => esc_html__( 'Content', 'elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Accordion Content', 'elementor' ),
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => esc_html__( 'Accordion Items', 'elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => esc_html__( 'Accordion #1', 'elementor' ),
						'tab_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
					],
					[
						'tab_title' => esc_html__( 'Accordion #2', 'elementor' ),
						'tab_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'separator' => 'before',
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'selected_active_icon',
			[
				'label' => esc_html__( 'Active Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_active',
				'default' => [
					'value' => 'fas fa-minus',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_item_title_icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'end' => [
						'title' => esc_html__( 'End', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'start' => '--n-accordion-title-icon-order: -1;',
					'end' => '--n-accordion-title-icon-order: initial;',
				],
				'selectors' => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'default' => 'end',
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'div',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'faq_schema',
			[
				'label' => esc_html__( 'FAQ Schema', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_interactions',
			[
				'label' => esc_html__( 'Interactions', 'elementor' ),
			]
		);

		$this->add_control(
			'default_state',
			[
				'label' => esc_html__( 'Default State', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'expanded' => esc_html__( 'First expanded', 'elementor' ),
					'all_collapsed' => esc_html__( 'All collapsed', 'elementor' ),
				],
				'default' => 'expanded',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'max_items_expended',
			[
				'label' => esc_html__( 'Max Items Expanded', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'one' => esc_html__( 'One', 'elementor' ),
					'multiple' => esc_html__( 'Multiple', 'elementor' ),
				],
				'default' => 'one',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'n_accordion_animation_duration',
			[
				'label' => esc_html__( 'Animation Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms' ],
				'default' => [
					'unit' => 'ms',
					'size' => 400,
				],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
		$this->add_style_tab();
	}

	private function add_style_tab() {
		$this->add_accordion_style_section();
		$this->add_header_style_section();
		$this->add_content_style_section();
	}

	private function add_accordion_style_section() {
		$this->start_controls_section(
			'section_accordion_style',
			[
				'label' => esc_html__( 'Accordion', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'accordion_item_title_space_between',
			[
				'label' => esc_html__( 'Space between Items', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--n-accordion-item-title-space-between: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_item_title_distance_from_content',
			[
				'label' => esc_html__( 'Distance from content', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--n-accordion-item-title-distance-from-content: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'accordion_border_and_background' );
		foreach ( [ 'normal', 'hover', 'active' ] as $state ) {
			$this->add_border_and_radius_style( $state );
		}
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'accordion_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--n-accordion-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'accordion_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--n-accordion-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_content_style_section() {
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .e-n-accordion-item > .e-n-accordion-item-content',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'selector' => '{{WRAPPER}} .e-n-accordion-item > .e-n-accordion-item-content',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-n-accordion-item > .e-n-accordion-item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_header_style_section() {
		$this->start_controls_section(
			'section_header_style',
			[
				'label' => esc_html__( 'Header', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .e-n-accordion-item-title-text',
			]
		);

		$this->start_controls_tabs( 'header_title_color_style' );
		foreach ( [ 'normal', 'hover', 'active' ] as $state ) {
			$this->add_header_style( $state, 'title' );
		}
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--n-accordion-icon-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'header_icon_color_style' );
		foreach ( [ 'normal', 'hover', 'active' ] as $state ) {
			$this->add_header_style( $state, 'icon' );
		}
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	private function add_header_style( $state, $context ) {
		$variable = '--n-accordion-' . $context . '-' . $state . '-color';
		$label = 'normal' === $state ? esc_html__( 'Normal', 'elementor' ) : ( 'hover' === $state ? esc_html__( 'Hover', 'elementor' ) : esc_html__( 'Active', 'elementor' ) );

		$this->start_controls_tab(
			'header_' . $state . '_' . $context,
			[
				'label' => $label,
			]
		);

		$this->add_control(
			$state . '_' . $context . '_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => $variable . ': {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
	}

	private function add_border_and_radius_style( $state ) {
		$selector = '{{WRAPPER}} .e-n-accordion > .e-n-accordion-item > .e-n-accordion-item-title';
		if ( 'hover' === $state ) {
			$selector .= ':hover';
		} elseif ( 'active' === $state ) {
			$selector = '{{WRAPPER}} .e-n-accordion > .e-n-accordion-item[open] > .e-n-accordion-item-title';
		}

		$this->start_controls_tab(
			'accordion_' . $state . '_border_and_background',
			[
				'label' => 'normal' === $state ? esc_html__( 'Normal', 'elementor' ) : ( 'hover' === $state ? esc_html__( 'Hover', 'elementor' ) : esc_html__( 'Active', 'elementor' ) ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'accordion_background_' . $state,
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => $selector,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'accordion_border_' . $state,
				'selector' => $selector,
			]
		);

		$this->end_controls_tab();
	}

    /**
     * Render accordion widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
  protected function render() {
        $settings = $this->get_settings_for_display();
        $migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
        $acf_field_key = $settings['acf_field_key'];
        $post_id = $this->get_context_post_id( $settings );
        $faq_rows = get_field( $acf_field_key, $post_id );
        if ( ! is_array( $faq_rows ) ) {
            $faq_rows = [];
        }

        $question_field_key = ! empty( $settings['question_field_key'] ) ? $settings['question_field_key'] : 'titolo_faq';
        $answer_field_key = ! empty( $settings['answer_field_key'] ) ? $settings['answer_field_key'] : 'testo_faq';

        if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
            // @todo: remove when deprecated
            // added as bc in 2.6
            // add old default
            $settings['icon'] = 'fa fa-plus';
            $settings['icon_active'] = 'fa fa-minus';
            $settings['icon_align'] = $this->get_settings( 'icon_align' );
        }

        $is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
        $has_icon = ( ! $is_new || ! empty( $settings['selected_icon']['value'] ) );
        $id_int = substr( $this->get_id_int(), 0, 3 );
        ?>
        <div class="e-n-accordion" aria-label="Accordion. Open links with Enter or Space, close with Escape, and navigate with Arrow Keys">
            <?php
            foreach ( $faq_rows as $index => $item ) :
                $tab_count = $index + 1;
   				$question = isset( $item[ $question_field_key ] ) ? $item[ $question_field_key ] : '';
                $answer = isset( $item[ $answer_field_key ] ) ? $item[ $answer_field_key ] : '';
                $item_id = 'e-n-accordion-item-' . $id_int . $tab_count;
                $tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );
                $tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

                $this->add_render_attribute( $tab_title_setting_key, [
                    'id' => 'elementor-tab-title-' . $id_int . $tab_count,
                    'class' => [ 'e-n-accordion-item-title' ],
                    'data-accordion-index' => $tab_count,
                    'tabindex' => 0 === $index ? 0 : -1,
                    'aria-controls' => $item_id,
                    'aria-expanded' => 0 === $index ? 'true' : 'false',
                ] );

                $this->add_render_attribute( $tab_content_setting_key, [
                    'id' => $item_id,
                    'class' => [ 'e-n-accordion-item-content' ],
                    'role' => 'region',
                    'aria-labelledby' => 'elementor-tab-title-' . $id_int . $tab_count,
                ] );
                ?>
                <details class="e-n-accordion-item e-normal" <?php echo ( ( $settings['default_state'] ?? 'expanded' ) === 'expanded' && 0 === $index ) ? 'open' : ''; ?>>
                    <summary <?php $this->print_render_attribute_string( $tab_title_setting_key ); ?>>
                        <?php if ( $has_icon ) : ?>
                            <span class="e-n-accordion-item-title-icon" aria-hidden="true">
                            <?php
                            if ( $is_new || $migrated ) { ?>
                                <span class="e-opened"><?php Icons_Manager::render_icon( $settings['selected_active_icon'] ); ?></span>
                                <span class="e-closed"><?php Icons_Manager::render_icon( $settings['selected_icon'] ); ?></span>
                            <?php } else { ?>
                                <span class="e-opened"><i class="<?php echo esc_attr( $settings['icon_active'] ); ?>"></i></span>
                                <span class="e-closed"><i class="<?php echo esc_attr( $settings['icon'] ); ?>"></i></span>
                            <?php } ?>
                            </span>
                        <?php endif; ?>
                        <span class="e-n-accordion-item-title-header">
                            <<?php Utils::print_validated_html_tag( $settings['title_html_tag'] ); ?> class="e-n-accordion-item-title-text"><?php
                                echo esc_html( $question );
                            ?></<?php Utils::print_validated_html_tag( $settings['title_html_tag'] ); ?>>
                        </span>
                    </summary>
                    <div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>>
                        <?php
                          echo wp_kses_post( $answer );
                        ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
        <?php
    }

    protected function content_template_single_repeater_item() {
        ?>
        <#
        const elementUid = view.getIDInt().toString().substring( 0, 3 ) + view.collection.length;
        const itemWrapperAttributes = {
            'id': 'e-n-accordion-item-' + elementUid,
            'class': [ 'e-n-accordion-item', 'e-normal' ],
        };
        const itemTitleAttributes = {
            'class': [ 'e-n-accordion-item-title' ],
            'data-accordion-index': view.collection.length + 1,
            'tabindex': -1,
            'aria-expanded': 'false',
            'aria-controls': 'e-n-accordion-item-' + elementUid,
        };
        const itemTitleTextAttributes = {
            'class': [ 'e-n-accordion-item-title-text' ],
            'data-binding-index': view.collection.length + 1,
            'data-binding-type': 'repeater-item',
            'data-binding-repeater-name': 'tabs',
            'data-binding-setting': [ 'tab_title' ],
            'data-binding-config': JSON.stringify({
                'tab_title': {
                    editType: 'text'
                }
            }),
        };
        view.addRenderAttribute( 'details-container', itemWrapperAttributes, null, true );
        view.addRenderAttribute( 'summary-container', itemTitleAttributes, null, true );
        view.addRenderAttribute( 'text-container', itemTitleTextAttributes, null, true );
        #>
        <details {{{ view.getRenderAttributeString( 'details-container' ) }}}>
            <summary {{{ view.getRenderAttributeString( 'summary-container' ) }}}>
                <span class="e-n-accordion-item-title-header">
                    <div {{{ view.getRenderAttributeString( 'text-container' ) }}}>{{{ data.tab_title }}}</div>
                </span>
                <span class="e-n-accordion-item-title-icon">
                    <span class="e-opened"><i aria-hidden="true" class="fas fa-minus"></i></span>
                    <span class="e-closed"><i aria-hidden="true" class="fas fa-plus"></i></span>
                </span>
            </summary>
        </details>
        <?php
    }

    protected function content_template() {
        ?>
        <div class="e-n-accordion" aria-label="Accordion. Open links with Enter or Space, close with Escape, and navigate with Arrow Keys">
            <# if ( settings['tabs'] ) {
            const elementUid = view.getIDInt().toString().substring( 0, 3 ),
                titleHTMLTag = elementor.helpers.validateHTMLTag( settings.title_html_tag ),
                itemTitleIcon = elementor.helpers.renderIcon( view, settings['selected_icon'], { 'aria-hidden': true }, 'i', 'object' ) ?? '',
                itemTitleIconActive = '' === settings.selected_active_icon.value
                    ? itemTitleIcon
                    : elementor.helpers.renderIcon( view, settings['selected_active_icon'], { 'aria-hidden': true }, 'i', 'object' );
            #>
                <# _.each( settings['tabs'], function( item, index ) {
                const itemCount = index + 1,
                    itemUid = elementUid + index,
                    itemTitleTextKey = 'item-title-text-' + itemUid,
                    itemWrapperKey = itemUid,
                    itemTitleKey = 'item-' + itemUid,
                    ariaExpanded = 0 === index ? 'true' : 'false',
                    itemId = 'e-n-accordion-item-' + itemUid;
                const itemWrapperAttributes = {
                    'id': itemId,
                    'class': [ 'e-n-accordion-item', 'e-normal' ],
                };
                if ( index === 0 ) {
                    itemWrapperAttributes['open'] = true;
                }
                view.addRenderAttribute( itemWrapperKey, itemWrapperAttributes );
                view.addRenderAttribute( itemTitleKey, {
                    'class': [ 'e-n-accordion-item-title' ],
                    'data-accordion-index': itemCount,
                    'tabindex': 0 === index ? 0 : -1,
                    'aria-expanded': ariaExpanded,
                    'aria-controls': itemId,
                });
                view.addRenderAttribute( itemTitleTextKey, {
                    'class': [ 'e-n-accordion-item-title-text' ],
                });
                #>
                <details {{{ view.getRenderAttributeString( itemWrapperKey ) }}}>
                    <summary {{{ view.getRenderAttributeString( itemTitleKey ) }}}>
                        <span class="e-n-accordion-item-title-header">
                            <{{{ titleHTMLTag }}} {{{ view.getRenderAttributeString( itemTitleTextKey ) }}}>
                                {{{ item.tab_title }}}
                            </{{{ titleHTMLTag }}}>
                        </span>
                        <# if ( settings.selected_icon && settings.selected_icon.value ) { #>
                        <span class="e-n-accordion-item-title-icon">
                            <span class="e-opened">{{{ itemTitleIconActive.value }}}</span>
                            <span class="e-closed">{{{ itemTitleIcon.value }}}</span>
                        </span>
                        <# } #>
                    </summary>
                </details>
                <# } ); #>
            <# } #>
        </div>
        <?php
    }


    
}

