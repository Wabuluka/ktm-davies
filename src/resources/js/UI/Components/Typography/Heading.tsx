import {
  Center,
  Heading as ChakraHeading,
  HStack,
  HeadingProps,
} from '@chakra-ui/react';

type Props = {
  icon?: React.ReactNode;
} & HeadingProps;

export function Heading({ icon, children, ...props }: Props) {
  return (
    <ChakraHeading size="md" {...props}>
      <HStack
        display={{ base: 'flex', lg: 'inline-flex' }}
        px={4}
        py={2}
        borderColor="cyan.800"
        borderBottomWidth={1}
        spacing={0}
      >
        <Center color="white" bg="cyan.500" borderRadius="full" p={2}>
          {icon}
        </Center>
        <Center px={{ base: 4, lg: 8 }}>{children}</Center>
      </HStack>
    </ChakraHeading>
  );
}
