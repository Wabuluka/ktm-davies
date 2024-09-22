import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
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
import { useCallback, useId, useState } from 'react';
import { useQueryClient } from 'react-query';
import { CreatorEventListenerProvider } from '../Contexts/CreatorEventListnerContext';
import { useCreateCreatorDrawer } from '../Hooks/useCreateCreatorDrawer';
import { useIndexCreatorQuery } from '../Hooks/useIndexCreatorQuery';
import { Creator } from '../Types';
import { CreatorList } from './CreatorList';
import {
  CreatorSearchForm,
  CreatorSearchFormParams,
} from './CreatorSearchForm';

type Props = {
  selectable?: boolean | ((creator: Creator) => boolean);
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (creator: Creator) => void;
};

export function SelectCreatorDrawer({
  isOpen,
  onClose,
  selectable,
  onSubmit,
}: Props) {
  const formId = useId();
  const queryClient = useQueryClient();
  const queryKeys = useQueryKeys().creators;
  const {
    data: paginator,
    isLoading,
    isError,
    setQueryParams,
  } = useIndexCreatorQuery();
  const { createCreatorDrawer, createCreatorDrawerOpenButton } =
    useCreateCreatorDrawer({
      buttonLabel: 'Create',
      onStoreSuccess: () => queryClient.invalidateQueries(queryKeys.all),
    });
  const [selectedCreatorId, setSelectedCreatorId] = useState<string>();
  const radioInput = {
    value: selectedCreatorId,
    ...useSelectInput((value) => setSelectedCreatorId(value)),
  };
  const selectedCreator = paginator?.data.find(
    (creator) => creator.id === Number(selectedCreatorId),
  );

  const handleSearchSubmit = useCallback(
    (params: CreatorSearchFormParams) => {
      setQueryParams({ ...params, page: 1 });
    },
    [setQueryParams],
  );
  const handleDeleteSuccess = useCallback(
    (creatorId: string) => {
      if (selectedCreatorId === creatorId) {
        setSelectedCreatorId(undefined);
      }
    },
    [selectedCreatorId],
  );
  const handlePageChange = useCallback(
    (page: number) => {
      setSelectedCreatorId(undefined);
      setQueryParams((prev) => ({ ...prev, page }));
    },
    [setQueryParams],
  );
  const handleSubmit = useCallback(
    (e: React.FormEvent<HTMLFormElement>) => {
      e.preventDefault();
      e.stopPropagation();
      if (!selectedCreator) {
        return;
      }
      onSubmit(selectedCreator);
      setSelectedCreatorId(undefined);
      setQueryParams({ keyword: '', page: 1 });
      onClose();
    },
    [onClose, onSubmit, selectedCreator, setQueryParams],
  );

  return (
    <CreatorEventListenerProvider onDeleteSuccess={handleDeleteSuccess}>
      <Drawer isOpen={isOpen} onClose={onClose}>
        <Text>Select Creator</Text>
        <VStack align="stretch" spacing={8}>
          <CreatorSearchForm onSubmit={handleSearchSubmit} />
          {isLoading ? (
            <LoadingSpinner />
          ) : isError || !paginator ? (
            <DataFetchError />
          ) : (
            <>
              <form onSubmit={handleSubmit} id={formId}>
                <RadioGroup {...radioInput}>
                  <CreatorList
                    creators={paginator.data}
                    selectable={selectable}
                    selectType="radio"
                  />
                </RadioGroup>
              </form>
              <PaginatorBase
                onPageChange={handlePageChange}
                lastPage={paginator.last_page}
                currentIndex={paginator.current_page}
              />
            </>
          )}
        </VStack>
        <Flex w="100%">
          {createCreatorDrawerOpenButton}
          <Spacer />
          <ButtonGroup>
            <Button onClick={onClose}>Back</Button>
            <PrimaryButton
              type="submit"
              form={formId}
              isDisabled={!selectedCreator}
            >
              Save
            </PrimaryButton>
          </ButtonGroup>
        </Flex>
      </Drawer>
      {createCreatorDrawer}
    </CreatorEventListenerProvider>
  );
}
