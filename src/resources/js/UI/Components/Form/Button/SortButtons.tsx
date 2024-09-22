import { ChevronDownIcon, ChevronUpIcon } from '@chakra-ui/icons';
import { Flex, IconButton, IconButtonProps } from '@chakra-ui/react';
import { FC, useCallback } from 'react';

type Props = {
  onUp?: () => void;
  onDown?: () => void;
  disableUp?: boolean;
  disableDown?: boolean;
};

export const SortButtons: FC<Props> = ({
  onUp,
  onDown,
  disableUp = false,
  disableDown = false,
}) => {
  const styles: Omit<IconButtonProps, 'aria-label'> = {
    bg: 'blue.500',
    color: 'white',
  };

  const disabledStyles: Omit<IconButtonProps, 'aria-label'> = {
    opacity: '0.3',
  };

  const onClickUp = useCallback(() => {
    if (disableUp) return;

    if (onUp) {
      onUp();
      return;
    }

    alert('TODO: Inplement sorting functionality');
  }, [disableUp, onUp]);

  const onClickDown = useCallback(() => {
    if (disableDown) return;

    if (onDown) {
      onDown();
      return;
    }

    alert('TODO: Implement sorting functionality');
  }, [disableDown, onDown]);

  return (
    <Flex gap={{ base: 1, md: 2 }} flexDir={{ base: 'column', md: 'row' }}>
      <IconButton
        as={ChevronUpIcon}
        aria-label="move up"
        onClick={onClickUp}
        disabled={disableUp}
        {...styles}
        {...(disableUp && {
          pointerEvents: 'none',
          _focus: {},
          ...disabledStyles,
        })}
      />{' '}
      <IconButton
        as={ChevronDownIcon}
        aria-label="move down"
        onClick={onClickDown}
        disabled={disableDown}
        {...styles}
        {...(disableDown && {
          pointerEvents: 'none',
          _focus: {},
          ...disabledStyles,
        })}
      />
    </Flex>
  );
};
