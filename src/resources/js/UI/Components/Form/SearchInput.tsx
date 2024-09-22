import { Search2Icon } from '@chakra-ui/icons';
import { HStack, IconButton, Input, InputProps } from '@chakra-ui/react';

export function SearchInput(props: Omit<InputProps, 'type'>) {
  return (
    <HStack spacing={0} borderRadius="16px" overflow="hidden">
      <Input
        type="search"
        borderLeftRadius="16px"
        borderRightRadius={0}
        {...props}
      />
      <IconButton
        type="submit"
        aria-label="Search"
        bg="cyan.500"
        borderRadius={0}
        color="white"
        icon={<Search2Icon />}
      />
    </HStack>
  );
}
