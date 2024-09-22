import { EbookStoreOnBookForm } from '@/Features/BookEbookStore/Types';
import {
  PropsWithChildren,
  createContext,
  useContext,
  useMemo,
  useReducer,
} from 'react';

type State = EbookStoreOnBookForm[];

type Action =
  | { type: 'set'; ebookstores: State }
  | { type: 'add'; ebookstore: EbookStoreOnBookForm }
  | {
      type: 'update';
      ebookstore: EbookStoreOnBookForm;
      id: EbookStoreOnBookForm['id'];
    }
  | {
      type: 'update-primary';
      id: EbookStoreOnBookForm['id'];
    }
  | {
      type: 'unset-primary';
    }
  | {
      type: 'delete';
      id: EbookStoreOnBookForm['id'];
    };

const reorder = (ebookstores: State) =>
  ebookstores.sort((a, b) => Number(a.id) - Number(b.id));

const ebookStoreReducer = (state: State, action: Action): State => {
  switch (action.type) {
    case 'set':
      return reorder(action.ebookstores);
    case 'add':
      return reorder([...state, action.ebookstore]);
    case 'update':
      return reorder(
        state.map((ebookstore) =>
          ebookstore.id === action.id ? action.ebookstore : ebookstore,
        ),
      );
    case 'update-primary':
      return state.map((ebookstore) =>
        ebookstore.id === action.id
          ? { ...ebookstore, is_primary: true }
          : { ...ebookstore, is_primary: false },
      );
    case 'unset-primary':
      return state.map((ebookstore) => ({
        ...ebookstore,
        is_primary: false,
      }));
    case 'delete':
      return state.filter((ebookstore) => ebookstore.id !== action.id);
  }
};

export const EbookStoresContext = createContext<{
  ebookstores?: State;
  selectedStoreIds?: number[];
  primaryStore?: EbookStoreOnBookForm;
}>({});

export const EbookStoresDispatchContext = createContext<
  React.Dispatch<Action> | undefined
>(undefined);

export function useEbookStores() {
  return useContext(EbookStoresContext);
}

export function useEbookStoresDispatch() {
  return useContext(EbookStoresDispatchContext);
}

export const EbookStoreDrawerProvider = ({
  initialState = [],
  children,
}: PropsWithChildren<{ initialState?: State }>) => {
  const [ebookstores, dispatch] = useReducer(ebookStoreReducer, initialState);
  const selectedStoreIds = useMemo(
    () => ebookstores.map((ebookstore) => Number(ebookstore.id)),
    [ebookstores],
  );
  const primaryStore = useMemo(
    () => ebookstores.find((ebookstore) => ebookstore.is_primary),
    [ebookstores],
  );

  return (
    <EbookStoresContext.Provider
      value={{
        ebookstores,
        selectedStoreIds,
        primaryStore,
      }}
    >
      <EbookStoresDispatchContext.Provider value={dispatch}>
        {children}
      </EbookStoresDispatchContext.Provider>
    </EbookStoresContext.Provider>
  );
};
