import { extendTheme } from '@chakra-ui/react';

export const theme = extendTheme({
  components: {
    Radio: {
      variants: {
        highlight: {
          control: {
            borderColor: 'gray.300',
          },
          container: {
            borderColor: 'transparent',
            borderRadius: 12,
            borderWidth: 1,
            p: 4,
            transition: '.2s',
            _hover: {
              bg: 'blue.50',
            },
            _checked: {
              bg: 'blue.50',
              borderColor: 'blue.100',
              shadow: 'sm',
            },
          },
        },
      },
    },
    Checkbox: {
      baseStyle: {
        control: {
          _disabled: {
            bg: 'gray.200',
            borderColor: 'gray.200',
          },
          _checked: {
            _disabled: {
              bg: 'gray.300',
              borderColor: 'gray.300',
              color: 'gray.600',
            },
          },
        },
      },
      variants: {
        highlight: {
          control: {
            borderColor: 'gray.300',
          },
          container: {
            borderColor: 'transparent',
            borderRadius: 12,
            borderWidth: 1,
            p: 4,
            transition: '.2s',
            _hover: {
              bg: 'blue.50',
            },
            _checked: {
              bg: 'blue.50',
              borderColor: 'blue.100',
              shadow: 'sm',
            },
          },
        },
      },
    },
  },
});
