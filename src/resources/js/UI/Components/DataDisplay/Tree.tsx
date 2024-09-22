import { ChevronDownIcon, ChevronRightIcon } from '@chakra-ui/icons';
import {
  Box,
  Collapse,
  HStack,
  IconButton,
  List,
  ListItem,
  Square,
  useDisclosure,
} from '@chakra-ui/react';
import { FC, useCallback, useEffect } from 'react';
import { TreeItem } from '../../Types';

type RenderItemProps<T> = {
  item: TreeItem<T>;
};

type NodeProps<T> = {
  item: TreeItem<T>;
  renderItem: FC<RenderItemProps<T>>;
  parentOnOpen?: () => void;
};

const Node = <T,>({
  item,
  renderItem: TreeItem,
  parentOnOpen,
}: NodeProps<T>) => {
  const { isOpen, onToggle, onOpen } = useDisclosure();

  const hasChildren = !!item.children && item.children.length > 0;

  useEffect(() => {
    if (parentOnOpen && item.isActive) parentOnOpen();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const openWithParent = useCallback(() => {
    parentOnOpen && parentOnOpen();
    onOpen();
  }, [onOpen, parentOnOpen]);

  return (
    <Box minW="100%" display="inline-block" verticalAlign="top">
      <HStack
        px={2}
        {...(item.isActive && {
          bg: 'cyan.500',
          borderColor: 'gray.100',
          borderLeftWidth: 2,
        })}
      >
        <Square minW={10} minH={10}>
          {hasChildren && (
            <IconButton
              w={1}
              h={1}
              p={1}
              icon={isOpen ? <ChevronDownIcon /> : <ChevronRightIcon />}
              aria-label={isOpen ? 'Close item' : 'Open item'}
              bg="transparent"
              opacity={0.6}
              _hover={{ opacity: 1 }}
              onClick={onToggle}
            />
          )}
        </Square>

        <TreeItem item={item} />
      </HStack>

      {hasChildren && (
        <Collapse in={isOpen}>
          <List ml={4}>
            {(item.children ?? []).map((child, i) => (
              <ListItem key={i}>
                <Node<T>
                  item={child}
                  renderItem={TreeItem}
                  parentOnOpen={openWithParent}
                />
              </ListItem>
            ))}
          </List>
        </Collapse>
      )}
    </Box>
  );
};

type RootProps<T> = Omit<NodeProps<T>, 'onOpen'>;

const Root = <T,>({ item, renderItem }: RootProps<T>) => {
  return <Node<T> item={item} renderItem={renderItem} />;
};

export { Root as Tree };
