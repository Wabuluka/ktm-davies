import { VStack } from '@chakra-ui/react';
import { ComponentProps } from 'react';

type Props = ComponentProps<'form'>;

export const SearchFormBase = ({ children, ...props }: Props) => {
  return (
    <form {...props}>
      <VStack
        align="stretch"
        bg="gray.50"
        borderColor="gray.300"
        borderRadius="md"
        borderWidth={1}
        shadow="sm"
        p={4}
        sx={{
          'input, select': { bg: 'white' },
        }}
      >
        {children}
      </VStack>
    </form>
  );
};
