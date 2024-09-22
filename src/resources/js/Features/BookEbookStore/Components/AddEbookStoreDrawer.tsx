import { EbookStoreOnBookForm } from '@/Features/BookEbookStore/Types';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId } from 'react';
import { useEbookStoresDispatch } from '../Contexts/EbookStoreDrawerContext';
import { EbookStoreForm } from './EbookStoreForm';

type Props = {
  isOpen: boolean;
  onClose: () => void;
};

export function AddEbookStoreDrawer({ isOpen, onClose }: Props) {
  const formId = useId();
  const dispatch = useEbookStoresDispatch();

  function handleSubmit(ebookstore: EbookStoreOnBookForm) {
    dispatch?.({ type: 'add', ebookstore });
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Add an Information of Purchase Option (eBook)</Text>
      <EbookStoreForm id={formId} onSubmit={handleSubmit} />
      <ButtonGroup>
        <Button onClick={onClose}>Back</Button>
        <PrimaryButton type="submit" form={formId}>
          Save
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
