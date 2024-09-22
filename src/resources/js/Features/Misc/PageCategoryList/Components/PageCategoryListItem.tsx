import { Link } from '@/UI/Components/Navigation/Link';
import { Method } from '@inertiajs/core/types';
import { Icon, LinkBox, Square, Text, VStack } from '@chakra-ui/react';
import { FC, ReactNode } from 'react';
import { IconType } from 'react-icons';

type Props = {
  href: string;
  method?: Method;
  selected?: boolean;
  icon: IconType;
  children: ReactNode;
};

export const PageCategoryListItem: FC<Props> = ({
  href,
  method,
  selected,
  icon,
  children,
}) => {
  return (
    <LinkBox
      as={Square}
      size={20}
      opacity={0.6}
      _hover={{ opacity: 1 }}
      {...(selected && {
        borderBottomWidth: 4,
        borderColor: 'pink.500',
        opacity: 1,
      })}
    >
      <VStack spacing={2}>
        <Icon as={icon} fontSize="2xl" />

        <Text fontSize="xx-small">
          <Link href={href} method={method} overlay>
            {children}
          </Link>
        </Text>
      </VStack>
    </LinkBox>
  );
};
