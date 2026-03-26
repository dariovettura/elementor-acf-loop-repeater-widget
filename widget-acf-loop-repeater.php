<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Utils;
use Elementor\Plugin;

class Elementor_Acf_Loop_Repeater_Widget extends Widget_Base {

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
        return [ 'widget-accordion' ];
    }

    public function get_categories() {
        return [ 'general' ];
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

		if ( Plugin::$instance->widgets_manager->get_widget_types( 'nested-accordion' ) ) {
			$this->add_deprecation_message(
				'3.15.0',
				esc_html__(
					'You are currently editing an Accordion Widget in its old version. Any new Accordion widget dragged into the canvas will be the new Accordion widget, with the improved Nested capabilities.',
					'elementor'
				),
				'nested-accordion'
			);
		}

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
			'section_title_style',
			[
				'label' => esc_html__( 'Accordion', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'border_width',
			[
				'label' => esc_html__( 'Border Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 20,
					],
					'em' => [
						'max' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-item' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-accordion-item .elementor-tab-content' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-accordion-item .elementor-tab-title.elementor-active' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-item' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-accordion-item .elementor-tab-content' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-accordion-item .elementor-tab-title.elementor-active' => 'border-bottom-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();




		$this->start_controls_section(
			'section_toggle_style_title',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_background',
			[
				'label' => esc_html__( 'Background', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-icon, {{WRAPPER}} .elementor-accordion-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-accordion-icon svg' => 'fill: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);

		$this->add_control(
			'tab_active_color',
			[
				'label' => esc_html__( 'Active Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-active .elementor-accordion-icon, {{WRAPPER}} .elementor-active .elementor-accordion-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-active .elementor-accordion-icon svg' => 'fill: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-accordion-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} .elementor-accordion-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_shadow',
				'selector' => '{{WRAPPER}} .elementor-accordion-title',
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Start', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => is_rtl() ? 'right' : 'left',
				'toggle' => false,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title .elementor-accordion-icon i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title .elementor-accordion-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label' => esc_html__( 'Active Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label' => esc_html__( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 1,
					],
					'rem' => [
						'max' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style_content',
			[
				'label' => esc_html__( 'Content', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_background_color',
			[
				'label' => esc_html__( 'Background', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .elementor-tab-content',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'content_shadow',
				'selector' => '{{WRAPPER}} .elementor-tab-content',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
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
        $faq_rows = get_field( $acf_field_key );

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
        <div class="elementor-accordion">
            <?php
            foreach ( $faq_rows as $index => $item ) :
                $tab_count = $index + 1;
   				$question = isset( $item['titolo_faq'] ) ? $item['titolo_faq'] : '';
                $answer = isset( $item['testo_faq'] ) ? $item['testo_faq'] : '';
                $tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

                $tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

                $this->add_render_attribute( $tab_title_setting_key, [
                    'id' => 'elementor-tab-title-' . $id_int . $tab_count,
                    'class' => [ 'elementor-tab-title' ],
                    'data-tab' => $tab_count,
                    'role' => 'button',
                    'aria-controls' => 'elementor-tab-content-' . $id_int . $tab_count,
                    'aria-expanded' => 'false',
                ] );

                $this->add_render_attribute( $tab_content_setting_key, [
                    'id' => 'elementor-tab-content-' . $id_int . $tab_count,
                    'class' => [ 'elementor-tab-content', 'elementor-clearfix' ],
                    'data-tab' => $tab_count,
                    'role' => 'region',
                    'aria-labelledby' => 'elementor-tab-title-' . $id_int . $tab_count,
                ] );

                $this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
                ?>
                <div class="elementor-accordion-item">
                    <<?php Utils::print_validated_html_tag( $settings['title_html_tag'] ); ?> <?php $this->print_render_attribute_string( $tab_title_setting_key ); ?>>
                        <?php if ( $has_icon ) : ?>
                            <span class="elementor-accordion-icon elementor-accordion-icon-<?php echo esc_attr( $settings['icon_align'] ); ?>" aria-hidden="true">
                            <?php
                            if ( $is_new || $migrated ) { ?>
                                <span class="elementor-accordion-icon-closed"><?php Icons_Manager::render_icon( $settings['selected_icon'] ); ?></span>
                                <span class="elementor-accordion-icon-opened"><?php Icons_Manager::render_icon( $settings['selected_active_icon'] ); ?></span>
                            <?php } else { ?>
                                <i class="elementor-accordion-icon-closed <?php echo esc_attr( $settings['icon'] ); ?>"></i>
                                <i class="elementor-accordion-icon-opened <?php echo esc_attr( $settings['icon_active'] ); ?>"></i>
                            <?php } ?>
                            </span>
                        <?php endif; ?>
                        <a class="elementor-accordion-title" tabindex="0"><?php
                          echo  esc_html( $question );
                        ?></a>
                    </<?php Utils::print_validated_html_tag( $settings['title_html_tag'] ); ?>>
                    <div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>>
                        <?php
                          echo wp_kses_post( $answer );
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }


    
}

