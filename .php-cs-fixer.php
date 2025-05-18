<?php

$config = (new PhpCsFixer\Config())->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
return $config->setRiskyAllowed(true)->setIndent('   ')
   ->setRules([
      '@PhpCsFixer'                              => true,
      'array_push'                               => true,
      'blank_line_between_import_groups'         => false,
      'combine_nested_dirname'                   => true,
      'comment_to_phpdoc'                        => true,
      'date_time_immutable'                      => true,
      'dir_constant'                             => true,
      'ereg_to_preg'                             => true,
      'fopen_flag_order'                         => true,
      'function_to_constant'                     => true,
      'get_class_to_class_keyword'               => true,
      'implode_call'                             => true,
      'logical_operators'                        => true,
      'long_to_shorthand_operator'               => true,
      'modernize_strpos'                         => true,
      'modernize_types_casting'                  => true,
      // 'multiline_promoted_properties'            => true,
      'multiline_string_to_heredoc'              => true,
      // 'new_expression_parentheses'               => true,
      'no_php4_constructor'                      => true,
      'no_unset_cast'                            => false,
      'no_useless_sprintf'                       => true,
      'octal_notation'                           => true,
      'ordered_interfaces'                       => true,
      'ordered_traits'                           => true,
      'phpdoc_param_order'                       => true,
      'phpdoc_readonly_class_comment_to_keyword' => true,
      'phpdoc_tag_casing'                        => true,
      'pow_to_exponentiation'                    => true,
      'psr_autoloading'                          => true,
      'random_api_migration'                     => true,
      'self_accessor'                            => true,
      'set_type_to_cast'                         => true,
      'simplified_if_return'                     => true,
      'strict_comparison'                        => true,
      'string_length_to_empty'                   => true,
      'ternary_to_elvis_operator'                => true,
      'ternary_to_null_coalescing'               => true,
      'use_arrow_functions'                      => true,
      'void_return'                              => true,

      'concat_space'                           => ['spacing' => 'one'],
      'fopen_flags'                            => ['b_mode' => false],
      'global_namespace_import'                => ['import_classes' => true],
      'increment_style'                        => ['style' => 'post'],
      'no_alias_functions'                     => ['sets' => ['@all']],
      'class_definition'                       => ['single_item_single_line' => true],
      'heredoc_indentation'                    => ['indentation' => 'same_as_start'],
      'list_syntax'                            => ['syntax' => 'long'],
      'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
      'no_break_comment'                       => ['comment_text' => 'continuous'],
      'non_printable_character'                => ['use_escape_sequences_in_strings' => false],
      'ordered_imports'                        => ['imports_order' => ['const', 'function', 'class']],
      'phpdoc_line_span'                       => ['const' => 'single', 'property' => 'single'],

      'binary_operator_spaces' => [
         'default'   => 'align',
      ],
      'blank_line_before_statement' => [
         'statements' => ['case', 'declare', 'default', 'do', 'for', 'foreach', 'goto', 'if', 'phpdoc', 'return', 'switch', 'throw', 'try', 'while', 'yield', 'yield_from']
      ],
      'class_attributes_separation' => [
         'elements' => ['const' => 'only_if_meta', 'property' => 'only_if_meta', 'trait_import' => 'one']
      ],
      'function_declaration' => [
         'closure_function_spacing' => 'none',
         'closure_fn_spacing' => 'none'
      ],
      'phpdoc_order_by_value' => [
         'annotations' => ['author', 'covers', 'coversNothing', 'dataProvider', 'depends', 'group', 'internal', 'method', 'mixin', 'property', 'property-read', 'property-write', 'requires', 'throws', 'uses']
      ],
      'trailing_comma_in_multiline' => [
         'elements' => ['arrays', 'array_destructuring', 'arguments', 'parameters', 'match']
      ],
      'no_extra_blank_lines' => [
         'tokens' => ['attribute', 'break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use']
      ],
      'yoda_style' => [
         'less_and_greater' => null,
         'always_move_variable' => true
      ],
   ]);
