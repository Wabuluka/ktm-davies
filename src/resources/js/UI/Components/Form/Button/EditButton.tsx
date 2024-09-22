import { EditIcon } from '@chakra-ui/icons';
import { forwardRef, IconButton, IconButtonProps } from '@chakra-ui/react';

export const EditButton = forwardRef<IconButtonProps, 'button'>(
  function EditButton(props, ref) {
    return (
      <IconButton
        as={EditIcon}
        cursor="pointer"
        bg="cyan.800"
        color="white"
        p={2}
        ref={ref}
        {...props}
      />
    );
  },
);
