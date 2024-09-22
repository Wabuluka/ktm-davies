import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId } from 'react';
import { useEbookStoresDispatch } from '../Contexts/EbookStoreDrawerContext';
import { EbookStoreOnBookForm } from '../Types';
import { EbookStoreForm } from './EbookStoreForm';

type Props = {
  ebookstore: EbookStoreOnBookForm;
  isOpen: boolean;
  onClose: () => void;
};

export function EditEbookStoreDrawer({
  ebookstore: initialValues,
  isOpen,
  onClose,
}: Props) {
  const formId = useId();
  const dispatch = useEbookStoresDispatch();

  function handleSubmit(ebookstore: EbookStoreOnBookForm) {
    dispatch?.({
      type: 'update',
      ebookstore,
      id: initialValues.id,
    });
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Edit an information of purchase option (eBook)</Text>
      <EbookStoreForm
        id={formId}
        initialValues={initialValues}
        onSubmit={handleSubmit}
      />
      <ButtonGroup>
        <Button onClick={onClose}>Back</Button>
        <PrimaryButton type="submit" form={formId}>
          Save
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
