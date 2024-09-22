import { SimpleGrid, SimpleGridProps } from '@chakra-ui/react';
import { FC } from 'react';

type Props = SimpleGridProps;

export const ResponsiveGrid: FC<Props> = (props) => {
  return <SimpleGrid columns={{ base: 1, lg: 2 }} spacing={4} {...props} />;
};
