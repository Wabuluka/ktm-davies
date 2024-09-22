import { useBlockTypes } from '@/Features/Block/Hooks/useBlockTypes';
import {
  useBookFormState,
  useSetBookFormData,
} from '@/Features/Book/Context/BookFormContext';
import { useBlockDispatcher } from '@/Features/Book/Hooks/useBlockDispatcher';
import RichTextEditor from '@/Features/RichTextEditor';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Heading } from '@/UI/Components/Typography/Heading';
import {
  Button,
  ButtonGroup,
  FormControl,
  RadioGroup,
  Text,
  VStack,
  useDisclosure,
} from '@chakra-ui/react';
import { useId, useMemo, useState } from 'react';
import {
  EbookStoreDrawerProvider,
  useEbookStores,
  useEbookStoresDispatch,
} from '../Contexts/EbookStoreDrawerContext';
import { EbookStoreOnBookForm } from '../Types';
import { AddEbookStoreDrawer } from './AddEbookStoreDrawer';
import { EbookStoreList } from './EbookStoreList';

type Props = {
  ebookstores: EbookStoreOnBookForm[];
  isOpen: boolean;
  onClose: () => void;
};

function EbookStoreDrawerBase({ isOpen, onClose }: Omit<Props, 'ebookstores'>) {
  const formId = useId();
  const addDrawerDisclosure = useDisclosure();
  const {
    data: { ebookstores: initialState, blocks },
  } = useBookFormState();
  const { setData } = useSetBookFormData();
  const { updateBlock } = useBlockDispatcher();
  const { isEbookStoreBlock } = useBlockTypes();
  const { ebookstores, primaryStore } = useEbookStores();
  const dispatch = useEbookStoresDispatch();

  const ebookStoreBlock = useMemo(
    () => blocks.upsert.find((block) => isEbookStoreBlock(block.type_id)),
    [blocks.upsert, isEbookStoreBlock],
  );
  const [ebookstoreBlockContent, setEbookstoreBlockContent] = useState(
    ebookStoreBlock?.custom_content || '',
  );

  function updateEbookStores() {
    if (!ebookstores) {
      return;
    }
    setData((prev) => ({
      ...prev,
      ebookstores,
    }));
  }
  function updateEbookStoreBlock() {
    if (!ebookStoreBlock) {
      return;
    }
    updateBlock({ custom_content: ebookstoreBlockContent }, ebookStoreBlock.id);
  }
  function handlePrimaryStoreChange(id: string) {
    dispatch?.({ type: 'update-primary', id });
  }
  function handlePrimaryStoreUnselect() {
    dispatch?.({ type: 'unset-primary' });
  }
  function handleBack() {
    dispatch?.({ type: 'set', ebookstores: initialState });
    onClose();
  }
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    updateEbookStores();
    updateEbookStoreBlock();
    onClose();
  }

  return (
    <>
      <Drawer isOpen={isOpen} onClose={handleBack}>
        <Text>Information of Purchase Option (eBooks)</Text>
        <form id={formId} onSubmit={handleSubmit}>
          <VStack align="stretch" spacing={8}>
            <Heading>List</Heading>
            <RadioGroup
              value={primaryStore?.id || ''}
              onChange={handlePrimaryStoreChange}
            >
              <EbookStoreList selectType="radio" />
            </RadioGroup>
            <ButtonGroup>
              <PrimaryButton onClick={addDrawerDisclosure.onOpen}>
                Add a Purchase Option
              </PrimaryButton>
              {!!primaryStore && (
                <Button onClick={handlePrimaryStoreUnselect}>
                  Don't show purchase option on list.
                </Button>
              )}
            </ButtonGroup>
          </VStack>
          <VStack align="stretch" spacing={8} mt={12}>
            <Heading>Comment</Heading>
            <FormControl>
              <RichTextEditor
                defaultValue={ebookstoreBlockContent}
                setValue={setEbookstoreBlockContent}
              />
            </FormControl>
          </VStack>
        </form>
        <ButtonGroup>
          <Button onClick={handleBack}>Back</Button>
          <PrimaryButton type="submit" form={formId}>
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
      <AddEbookStoreDrawer
        isOpen={addDrawerDisclosure.isOpen}
        onClose={addDrawerDisclosure.onClose}
      />
    </>
  );
}

export function EbookStoreDrawer({ ebookstores, isOpen, onClose }: Props) {
  return (
    <EbookStoreDrawerProvider initialState={ebookstores}>
      <EbookStoreDrawerBase isOpen={isOpen} onClose={onClose} />
    </EbookStoreDrawerProvider>
  );
}
