import { Center, Grid, Image, ImageProps } from '@chakra-ui/react';

export function ImagePreview({ children, ...props }: ImageProps) {
  return (
    <Grid w="100%">
      <Center
        gridArea="1/-1"
        backdropBrightness="60%"
        backdropFilter="auto"
        bg="gray.100"
        borderColor="gray.300"
        borderWidth={1}
        p={4}
        w="100%"
      >
        <Image boxSize="xs" objectFit="contain" {...props} />
      </Center>
      <Center gridArea="1/-1">{children}</Center>
    </Grid>
  );
}
