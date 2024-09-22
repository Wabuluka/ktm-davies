import { useSetBookFormData } from '@/Features/Book/Context/BookFormContext';
import { useCallback } from 'react';
import { CreationFormData, CreationOnBookForm } from '../Types';

export function useBookCreationDispatcher() {
  const { setData } = useSetBookFormData();

  const updateCreationOnBookForm = useCallback(
    (callback: (creations: CreationOnBookForm[]) => CreationOnBookForm[]) => {
      setData(({ creations, ...rest }) => ({
        ...rest,
        creations: callback(creations),
      }));
    },
    [setData],
  );

  const addCreation = useCallback(
    (creation: CreationFormData) => {
      updateCreationOnBookForm((prev) => [
        ...prev,
        { ...creation, sort: prev.length + 1 },
      ]);
    },
    [updateCreationOnBookForm],
  );

  const updateCreation = useCallback(
    (creation: CreationFormData, prevCreatorId: string) => {
      updateCreationOnBookForm((prev) =>
        prev.map((prev) =>
          prev.creator_id === prevCreatorId
            ? {
                ...prev,
                creator_id: creation.creator_id,
                creation_type: creation.creation_type,
                displayed_type: creation.displayed_type,
              }
            : prev,
        ),
      );
    },
    [updateCreationOnBookForm],
  );

  const deleteCreation = useCallback(
    (creationId: string) => {
      updateCreationOnBookForm((prev) =>
        prev
          .filter((prev) => prev.creator_id !== creationId)
          .map((prev, i) => ({ ...prev, sort: i + 1 })),
      );
    },
    [updateCreationOnBookForm],
  );

  const moveUpCreation = useCallback(
    (creation: CreationOnBookForm) => {
      if (creation.sort < 2) {
        throw new Error('The sorting order is invalid.');
      }
      updateCreationOnBookForm((prev) =>
        prev
          .map((prev) => {
            if (creation.creator_id === prev.creator_id) {
              return { ...prev, sort: prev.sort - 1 };
            }
            if (creation.sort - 1 === prev.sort) {
              return { ...prev, sort: prev.sort + 1 };
            }
            return prev;
          })
          .sort((a, b) => a.sort - b.sort),
      );
    },
    [updateCreationOnBookForm],
  );

  const moveDownCreation = useCallback(
    (creation: CreationOnBookForm) => {
      updateCreationOnBookForm((prev) => {
        if (creation.sort === prev.length) {
          throw new Error('The sorting order is invalid');
        }
        const newCreations = prev
          .map((prev) => {
            if (creation.creator_id === prev.creator_id) {
              return { ...prev, sort: prev.sort + 1 };
            }
            if (creation.sort + 1 === prev.sort) {
              return { ...prev, sort: prev.sort - 1 };
            }
            return prev;
          })
          .sort((a, b) => a.sort - b.sort);
        return newCreations;
      });
    },
    [updateCreationOnBookForm],
  );

  return {
    addCreation,
    updateCreation,
    deleteCreation,
    moveUpCreation,
    moveDownCreation,
  };
}
