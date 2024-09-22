import { Link } from '@/UI/Components/Navigation/Link';
import { LinkBox, Text } from '@chakra-ui/react';
import { FC } from 'react';
import { Page } from '../Types';

type Props = {
  item: Page;
};

export const PageListItem: FC<Props> = ({ item }) => {
  return (
    <Text as={LinkBox} w="100%" py={4}>
      {item.href ? (
        <Link href={item.href} overlay preserveState>
          {item.name}
        </Link>
      ) : (
        item.name
      )}
    </Text>
  );
};
