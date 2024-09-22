import { useDisclosure } from '@chakra-ui/react';
import { SelectBookDrawer } from '../Components/SelectBookDrawer';
import { Book } from '../Types';

type Props = {
  onSubmit: (book: Book) => void;
};

export function useSelectBookDrawer({ onSubmit }: Props) {
  const disclosure = useDisclosure();

  const selectBookDrawer = (
    <SelectBookDrawer
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
      onSubmit={onSubmit}
    />
  );

  return {
    ...disclosure,
    selectBookDrawer,
  };
}
