import {
  useDispatchRelatedItems,
  useRelatedItems,
} from '@/Features/RelatedItem/Contexts/RelatedItemsContext';
import { useSelectInput } from '@/Hooks/Form/useSelectInput';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { PaginatorBase } from '@/UI/Components/Navigation/PaginatorBase';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import {
  Button,
  ButtonGroup,
  Flex,
  RadioGroup,
  Spacer,
  Text,
  VStack,
} from '@chakra-ui/react';
import { FormEvent, useId, useMemo, useState } from 'react';
import { useQueryClient } from 'react-query';
import { useCreateExternalLinkDrawer } from '../Hooks/useCreateExternalLinkDrawer';
import { useEditExternalLinkDrawer } from '../Hooks/useEditExternalLinkDrawer';
import { useIndexExternalLinkQuery } from '../Hooks/useIndexExternalLinkQuery';
import { ExternalLink } from '../Types';
import { ExternalLinkList } from './ExternalLinkList';
import {
  ExternalLinkSearchForm,
  SearchParameters,
} from './ExternalLinkSearchForm';

type Props = {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (externalLink: ExternalLink) => void;
};

export function SelectExternalLinkDrawer({ isOpen, onClose, onSubmit }: Props) {
  const formId = useId();
  const queryClient = useQueryClient();
  const {
    data: paginator,
    isError,
    isLoading,
    queryKey,
    setQueryParams,
  } = useIndexExternalLinkQuery();
  const [selectedLinkId, setSelectedLinkId] = useState<string>();
  const [editingLink, setEditingLink] = useState<ExternalLink | null>(null);
  const relatedItems = useRelatedItems();
  const dispatch = useDispatchRelatedItems();
  const addedExternalLinks = relatedItems?.upsert.filter(
    (item) => item.relatable_type === 'externalLink',
  );
  const { onOpen: onOpenCreateDrawer, createExternalLinkDrawer } =
    useCreateExternalLinkDrawer({
      onStoreSuccess: () => {
        queryClient.invalidateQueries(queryKey);
      },
    });
  const { onOpen: onOpenEditDrawer, editExternalLinkDrawer } =
    useEditExternalLinkDrawer({
      externalLink: editingLink,
      onDeleteSuccess: (externalLink) => {
        const noLonger = addedExternalLinks?.find(
          (item) => item.relatable_id === String(externalLink.id),
        );
        if (noLonger) {
          dispatch?.({ type: 'delete', id: noLonger.id });
        }
        setEditingLink(null);
        queryClient.invalidateQueries(queryKey);
      },
      onUpdateSuccess: () => {
        setEditingLink(null);
        queryClient.invalidateQueries(queryKey);
      },
    });
  const radioInput = {
    value: selectedLinkId,
    ...useSelectInput((value) => setSelectedLinkId(value)),
  };
  const selectedLink = useMemo(
    () => paginator?.data.find((item) => item.id === Number(selectedLinkId)),
    [paginator?.data, selectedLinkId],
  );
  function selectable(externalLink: ExternalLink) {
    if (!addedExternalLinks) {
      return true;
    }
    return addedExternalLinks.every(
      (item) => item.relatable_id !== String(externalLink.id),
    );
  }
  function handleSearchSubmit(params: SearchParameters) {
    setSelectedLinkId(undefined);
    setQueryParams({ page: 1, ...params });
  }
  function handlePageChange(page: number) {
    setSelectedLinkId(undefined);
    setQueryParams((prev) => ({ ...prev, page }));
  }
  function handleEditButtonClick(externalLink: ExternalLink) {
    setEditingLink(externalLink);
    onOpenEditDrawer();
  }
  function handleSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    if (!selectedLink) {
      return;
    }
    onSubmit(selectedLink);
    onClose();
  }

  return (
    <>
      <Drawer isOpen={isOpen} onClose={onClose}>
        <Text>Select from External Link</Text>
        <VStack align="stretch" spacing={8}>
          <ExternalLinkSearchForm onSubmit={handleSearchSubmit} />
          {isLoading ? (
            <LoadingSpinner />
          ) : isError || !paginator ? (
            <DataFetchError />
          ) : (
            <>
              <form id={formId} onSubmit={handleSubmit}>
                <RadioGroup {...radioInput}>
                  <ExternalLinkList
                    externalLinks={paginator.data}
                    onLinkEdit={handleEditButtonClick}
                    selectable={selectable}
                  />
                </RadioGroup>
              </form>
              <PaginatorBase
                onPageChange={handlePageChange}
                lastPage={paginator.meta.last_page}
                currentIndex={paginator.meta.current_page}
              />
            </>
          )}
        </VStack>
        <Flex w="100%">
          <PrimaryButton onClick={onOpenCreateDrawer}>Create</PrimaryButton>
          <Spacer />
          <ButtonGroup>
            <Button onClick={onClose}>Back</Button>
            <PrimaryButton
              form={formId}
              isDisabled={!selectedLinkId}
              type="submit"
            >
              Select
            </PrimaryButton>
          </ButtonGroup>
        </Flex>
      </Drawer>
      {createExternalLinkDrawer}
      {editExternalLinkDrawer}
    </>
  );
}
